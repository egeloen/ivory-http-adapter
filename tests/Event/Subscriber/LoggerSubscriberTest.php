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
use Ivory\HttpAdapter\Event\Timer\TimerInterface;

/**
 * Logger subscriber test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class LoggerSubscriberTest extends AbstractSubscriberTest
{
    /** @var \Ivory\HttpAdapter\Event\Subscriber\LoggerSubscriber */
    private $loggerSubscriber;

    /** @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $logger;

    /** @var \Ivory\HttpAdapter\Event\Formatter\FormatterInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $formatter;

    /** @var \Ivory\HttpAdapter\Event\Timer\TimerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $timer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->loggerSubscriber = new LoggerSubscriber(
            $this->logger = $this->createLoggerMock(),
            $this->formatter = $this->createFormatterMock(),
            $this->timer = $this->createTimerMock()
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->timer);
        unset($this->formatter);
        unset($this->logger);
        unset($this->loggerSubscriber);
    }

    public function testDefaultState()
    {
        $this->loggerSubscriber = new LoggerSubscriber($this->logger);

        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Subscriber\AbstractFormatterSubscriber',
            $this->loggerSubscriber
        );

        $this->assertSame($this->logger, $this->loggerSubscriber->getLogger());

        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Formatter\Formatter',
            $this->loggerSubscriber->getFormatter()
        );

        $this->assertInstanceOf('Ivory\HttpAdapter\Event\Timer\Timer', $this->loggerSubscriber->getTimer());
    }

    public function testInitialState()
    {
        $this->assertSame($this->logger, $this->loggerSubscriber->getLogger());
        $this->assertSame($this->formatter, $this->loggerSubscriber->getFormatter());
        $this->assertSame($this->timer, $this->loggerSubscriber->getTimer());
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
        $this->timer
            ->expects($this->once())
            ->method('start')
            ->with($this->identicalTo($request = $this->createRequestMock()));

        $this->timer
            ->expects($this->once())
            ->method('stop')
            ->with($this->identicalTo($request));

        $this->formatter
            ->expects($this->once())
            ->method('formatHttpAdapter')
            ->with($this->identicalTo($httpAdapter = $this->createHttpAdapterMock()))
            ->will($this->returnValue($formattedHttpAdapter = 'http_adapter'));

        $this->formatter
            ->expects($this->once())
            ->method('formatRequest')
            ->with($this->identicalTo($request))
            ->will($this->returnValue($formattedRequest = 'request'));

        $this->formatter
            ->expects($this->once())
            ->method('formatResponse')
            ->with($this->identicalTo($response = $this->createResponseMock()))
            ->will($this->returnValue($formattedResponse = 'response'));

        $this->logger
            ->expects($this->once())
            ->method('debug')
            ->with(
                $this->matchesRegularExpression('/^Send "GET http:\/\/egeloen\.fr" in [0-9]+\.[0-9]{2} ms\.$/'),
                $this->identicalTo(array(
                    'adapter'  => $formattedHttpAdapter,
                    'request'  => $formattedRequest,
                    'response' => $formattedResponse,
                ))
            );

        $this->loggerSubscriber->onPreSend($this->createPreSendEvent($httpAdapter, $request));
        $this->loggerSubscriber->onPostSend($this->createPostSendEvent($httpAdapter, $request, $response));
    }

    public function testExceptionEvent()
    {
        $this->timer
            ->expects($this->once())
            ->method('start')
            ->with($this->identicalTo($request = $this->createRequestMock()));

        $this->timer
            ->expects($this->once())
            ->method('stop')
            ->with($this->identicalTo($request));

        $this->formatter
            ->expects($this->once())
            ->method('formatHttpAdapter')
            ->with($this->identicalTo($httpAdapter = $this->createHttpAdapterMock()))
            ->will($this->returnValue($formattedHttpAdapter = 'http_adapter'));

        $this->formatter
            ->expects($this->once())
            ->method('formatRequest')
            ->with($this->identicalTo($request))
            ->will($this->returnValue($formattedRequest = 'request'));

        $this->formatter
            ->expects($this->once())
            ->method('formatResponse')
            ->with($this->identicalTo($response = $this->createResponseMock()))
            ->will($this->returnValue($formattedResponse = 'response'));

        $this->formatter
            ->expects($this->once())
            ->method('formatException')
            ->with($this->identicalTo($exception = $this->createExceptionMock($request, $response)))
            ->will($this->returnValue($formattedException = 'exception'));

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with(
                $this->identicalTo('Unable to send "GET http://egeloen.fr".'),
                $this->identicalTo(array(
                    'adapter'   => $formattedHttpAdapter,
                    'exception' => $formattedException,
                    'request'   => $formattedRequest,
                    'response'  => $formattedResponse,
                ))
            );

        $this->loggerSubscriber->onPreSend($this->createPreSendEvent($httpAdapter, $request));
        $this->loggerSubscriber->onException($this->createExceptionEvent($httpAdapter, $exception));
    }

    /**
     * {@inheritdoc}
     */
    protected function createRequestMock()
    {
        $request = parent::createRequestMock();

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
            ->method('getParameter')
            ->with($this->identicalTo(TimerInterface::TIME))
            ->will($this->returnValue(123));

        return $request;
    }

    /**
     * Creates a logger mock.
     *
     * @return \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject The logger mock.
     */
    private function createLoggerMock()
    {
        return $this->getMock('Psr\Log\LoggerInterface');
    }

    /**
     * Creates a formatter mock.
     *
     * @return \Ivory\HttpAdapter\Event\Formatter\FormatterInterface|\PHPUnit_Framework_MockObject_MockObject The formatter mock.
     */
    private function createFormatterMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Event\Formatter\FormatterInterface');
    }

    /**
     * Creates a timer mock.
     *
     * @return \Ivory\HttpAdapter\Event\Timer\TimerInterface|\PHPUnit_Framework_MockObject_MockObject The timer mock.
     */
    private function createTimerMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Event\Timer\TimerInterface');
    }
}
