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
use Ivory\HttpAdapter\Event\ExceptionEvent;
use Ivory\HttpAdapter\Event\PostSendEvent;
use Ivory\HttpAdapter\Event\PreSendEvent;
use Ivory\HttpAdapter\Event\Subscriber\LoggerSubscriber;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\InternalRequest;
use Ivory\HttpAdapter\Message\Response;
use Ivory\HttpAdapter\Message\Stream\StringStream;

/**
 * Logger subscriber test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class LoggerSubscriberTest extends \PHPUnit_Framework_TestCase
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
        $this->logger = $this->getMock('Psr\Log\LoggerInterface');
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

    public function testSubscribedEvents()
    {
        $events = LoggerSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::PRE_SEND, $events);
        $this->assertSame('onPreSend', $events[Events::PRE_SEND]);

        $this->assertArrayHasKey(Events::POST_SEND, $events);
        $this->assertSame('onPostSend', $events[Events::POST_SEND]);

        $this->assertArrayHasKey(Events::EXCEPTION, $events);
        $this->assertSame('onException', $events[Events::EXCEPTION]);
    }

    public function testPostSendEvent()
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $this->logger
            ->expects($this->once())
            ->method('debug')
            ->with(
                $this->matchesRegularExpression('/^Send "GET http:\/\/egeloen\.fr" in [0-9]+\.[0.9]{2} ms\.$/'),
                $this->callback(function ($context) use ($request, $response) {
                    return $context['time'] > 0 && $context['time'] < 1
                        && $context['request']['protocol_version'] === $request->getProtocolVersion()
                        && $context['request']['url'] === $request->getUrl()
                        && $context['request']['method'] === $request->getMethod()
                        && $context['request']['headers'] === $request->getHeaders()
                        && $context['request']['data'] === $request->getData()
                        && $context['request']['files'] === $request->getFiles()
                        && $context['response']['protocol_version'] === $response->getProtocolVersion()
                        && $context['response']['status_code'] === $response->getStatusCode()
                        && $context['response']['reason_phrase'] === $response->getReasonPhrase()
                        && $context['response']['headers'] === $response->getHeaders()
                        && $context['response']['body'] === (string) $response->getBody()
                        && $context['response']['effective_url'] === $response->getEffectiveUrl();
                })
            );

        $this->loggerSubscriber->onPreSend(new PreSendEvent($request));
        $this->loggerSubscriber->onPostSend(new PostSendEvent($request, $response));
    }

    public function testExceptionEvent()
    {
        $request = $this->createRequest();
        $exception = $this->createException();

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
                        && $context['request']['data'] === $request->getData()
                        && $context['request']['files'] === $request->getFiles()
                        && $context['exception']['code'] === $exception->getCode()
                        && $context['exception']['message'] === $exception->getMessage()
                        && $context['exception']['line'] === 168
                        && $context['exception']['file'] === __FILE__;
                })
            );

        $this->loggerSubscriber->onException(new ExceptionEvent($request, $exception));
    }

    /**
     * Creates a request.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequest The request.
     */
    protected function createRequest()
    {
        $request = new InternalRequest('http://egeloen.fr', InternalRequest::METHOD_GET);
        $request->setProtocolVersion(InternalRequest::PROTOCOL_VERSION_10);
        $request->setHeaders(array('connection' => 'close'));
        $request->setData(array('foo' => 'bar'));
        $request->setFiles(array('file' => __FILE__));

        return $request;
    }

    /**
     * Creates a response.
     *
     * @return \Ivory\HttpAdapter\Message\Response The response.
     */
    protected function createResponse()
    {
        $response = new Response();
        $response->setProtocolVersion(Response::PROTOCOL_VERSION_11);
        $response->setStatusCode(200);
        $response->setReasonPhrase('OK');
        $response->setHeaders(array('transfer-encoding' => 'chunked'));
        $response->setBody(new StringStream('foo'));
        $response->setEffectiveUrl('http://www.google.com');

        return $response;
    }

    /**
     * Creates an exception.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException The exception.
     */
    protected function createException()
    {
        return new HttpAdapterException('message', 123);
    }
}
