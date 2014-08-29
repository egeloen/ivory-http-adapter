<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\Subscriber;

use Ivory\HttpAdapter\Event\Events;
use Ivory\HttpAdapter\Event\Subscriber\LoggerSubscriber;

/**
 * Logger subscriber test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class LoggerSubscriberTest extends AbstractSubscriberTest
{
    /** @var \Ivory\HttpAdapter\Event\Subscriber\LoggerSubscriber */
    protected $loggerSubscriber;

    /** @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $logger;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->logger = $this->createLoggerMock();
        $this->loggerSubscriber = new LoggerSubscriber($this->logger);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->logger);
        unset($this->loggerSubscriber);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->logger, $this->loggerSubscriber->getLogger());
    }

    public function testSetLogger()
    {
        $this->loggerSubscriber->setLogger($logger = $this->createLoggerMock());

        $this->assertSame($logger, $this->loggerSubscriber->getLogger());
    }

    public function testSubscribedEvents()
    {
        $events = LoggerSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::PRE_SEND, $events);
        $this->assertSame(array('onPreSend', 100), $events[Events::PRE_SEND]);

        $this->assertArrayHasKey(Events::POST_SEND, $events);
        $this->assertSame(array('onPostSend', 100), $events[Events::POST_SEND]);

        $this->assertArrayHasKey(Events::EXCEPTION, $events);
        $this->assertSame(array('onException', 100), $events[Events::EXCEPTION]);
    }

    public function testPostSendEvent()
    {
        $request = $this->createRequestMock();
        $response = $this->createResponseMock();

        $this->logger
            ->expects($this->once())
            ->method('debug')
            ->with(
                $this->matchesRegularExpression('/^Send "GET http:\/\/egeloen\.fr" in [0-9]+\.[0-9]{2} ms\.$/'),
                $this->callback(function ($context) use ($request, $response) {
                    return $context['time'] > 0 && $context['time'] < 1
                        && $context['request']['protocol_version'] === $request->getProtocolVersion()
                        && $context['request']['url'] === $request->getUrl()
                        && $context['request']['method'] === $request->getMethod()
                        && $context['request']['headers'] === $request->getHeaders()
                        && $context['request']['raw_datas'] === $request->getRawDatas()
                        && $context['request']['datas'] === $request->getDatas()
                        && $context['request']['files'] === $request->getFiles()
                        && $context['request']['parameters'] === $request->getParameters()
                        && $context['response']['protocol_version'] === $response->getProtocolVersion()
                        && $context['response']['status_code'] === $response->getStatusCode()
                        && $context['response']['reason_phrase'] === $response->getReasonPhrase()
                        && $context['response']['headers'] === $response->getHeaders()
                        && $context['response']['body'] === (string) $response->getBody()
                        && $context['response']['parameters'] === $response->getParameters();
                })
            );

        $this->loggerSubscriber->onPreSend($this->createPreSendEvent(null, $request));
        $this->loggerSubscriber->onPostSend($this->createPostSendEvent(null, $request, $response));
    }

    public function testExceptionEvent()
    {
        $request = $this->createRequestMock();
        $exception = $this->createExceptionMock();

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with(
                $this->identicalTo('Unable to send "GET http://egeloen.fr".'),
                $this->callback(function ($context) use ($request, $exception) {
                    return $context['request']['protocol_version'] === $request->getProtocolVersion()
                        && $context['request']['url'] === $request->getUrl()
                        && $context['request']['method'] === $request->getMethod()
                        && $context['request']['headers'] === $request->getHeaders()
                        && $context['request']['raw_datas'] === $request->getRawDatas()
                        && $context['request']['datas'] === $request->getDatas()
                        && $context['request']['files'] === $request->getFiles()
                        && $context['request']['parameters'] === $request->getParameters()
                        && $context['exception']['code'] === $exception->getCode()
                        && $context['exception']['message'] === $exception->getMessage()
                        && $context['exception']['line'] === $exception->getLine()
                        && $context['exception']['file'] === $exception->getFile();
                })
            );

        $this->loggerSubscriber->onException($this->createExceptionEvent(null, $request, $exception));
    }

    /**
     * Creates a logger mock.
     *
     * @return \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject The logger mock.
     */
    protected function createLoggerMock()
    {
        return $this->getMock('Psr\Log\LoggerInterface');
    }

    /**
     * {@inheritdoc}
     */
    protected function createRequestMock()
    {
        $request = parent::createRequestMock();
        $request
            ->expects($this->any())
            ->method('getProtocolVersion')
            ->will($this->returnValue('1.1'));

        $request
            ->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue('http://egeloen.fr'));

        $request
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue('GET'));

        $request
            ->expects($this->any())
            ->method('getHeaders')
            ->will($this->returnValue(array('foo' => 'bar')));

        $request
            ->expects($this->any())
            ->method('getRawDatas')
            ->will($this->returnValue('foo=bar'));

        $request
            ->expects($this->any())
            ->method('getDatas')
            ->will($this->returnValue(array('baz' => 'bat')));

        $request
            ->expects($this->any())
            ->method('getFiles')
            ->will($this->returnValue(array('bit' => __FILE__)));

        $request
            ->expects($this->any())
            ->method('getParamters')
            ->will($this->returnValue(array('ban' => 'bor')));

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    protected function createResponseMock()
    {
        $response = parent::createResponseMock();
        $response
            ->expects($this->any())
            ->method('getProtocolVersion')
            ->will($this->returnValue('1.1'));

        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(200));

        $response
            ->expects($this->any())
            ->method('getReasonPhrase')
            ->will($this->returnValue('OK'));

        $response
            ->expects($this->any())
            ->method('getHeaders')
            ->will($this->returnValue(array('bal' => 'bol')));

        $response
            ->expects($this->any())
            ->method('getStream')
            ->will($this->returnValue('stream'));

        $response
            ->expects($this->any())
            ->method('getParameters')
            ->will($this->returnValue(array('bil' => 'bob')));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    protected function createExceptionMock()
    {
        $exception = parent::createExceptionMock();
        $exception
            ->expects($this->any())
            ->method('getCode')
            ->will($this->returnValue(123));

        $exception
            ->expects($this->any())
            ->method('getMessage')
            ->will($this->returnValue('message'));

        $exception
            ->expects($this->any())
            ->method('getLine')
            ->will($this->returnValue(234));

        $exception
            ->expects($this->any())
            ->method('getFile')
            ->will($this->returnValue(__FILE__));

        return $exception;
    }
}
