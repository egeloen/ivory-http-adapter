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
use Ivory\HttpAdapter\Message\InternalRequestInterface;

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

    public function testSubscribedEvents()
    {
        $events = LoggerSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::REQUEST_CREATED, $events);
        $this->assertSame(array('onRequestCreated', 100), $events[Events::REQUEST_CREATED]);

        $this->assertArrayHasKey(Events::REQUEST_SENT, $events);
        $this->assertSame(array('onRequestSent', 100), $events[Events::REQUEST_SENT]);

        $this->assertArrayHasKey(Events::REQUEST_ERRORED, $events);
        $this->assertSame(array('onRequestErrored', 100), $events[Events::REQUEST_ERRORED]);

        $this->assertArrayHasKey(Events::MULTI_REQUEST_CREATED, $events);
        $this->assertSame(array('onMultiRequestCreated', 100), $events[Events::MULTI_REQUEST_CREATED]);

        $this->assertArrayHasKey(Events::MULTI_REQUEST_SENT, $events);
        $this->assertSame(array('onMultiRequestSent', 100), $events[Events::MULTI_REQUEST_SENT]);

        $this->assertArrayHasKey(Events::MULTI_REQUEST_ERRORED, $events);
        $this->assertSame(array('onMultiResponseErrored', 100), $events[Events::MULTI_REQUEST_ERRORED]);
    }

    public function testRequestSentEvent()
    {
        $this->timer
            ->expects($this->once())
            ->method('start')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue($startedRequest = $this->createRequestMock()));

        $this->timer
            ->expects($this->once())
            ->method('stop')
            ->with($this->identicalTo($startedRequest))
            ->will($this->returnValue($stoppedRequest = $this->createRequestMock()));

        $this->formatter
            ->expects($this->once())
            ->method('formatRequest')
            ->with($this->identicalTo($stoppedRequest))
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
                    'adapter'  => 'http_adapter',
                    'request'  => $formattedRequest,
                    'response' => $formattedResponse,
                ))
            );

        $this->loggerSubscriber->onRequestCreated($requestCreatedEvent = $this->createRequestCreatedEvent(null, $request));
        $this->loggerSubscriber->onRequestSent($requestSentEvent = $this->createRequestSentEvent(null, $startedRequest, $response));

        $this->assertSame($startedRequest, $requestCreatedEvent->getRequest());
        $this->assertSame($stoppedRequest, $requestSentEvent->getRequest());
    }

    public function testRequestErroredEvent()
    {
        $this->timer
            ->expects($this->once())
            ->method('start')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue($startedRequest = $this->createRequestMock()));

        $this->timer
            ->expects($this->once())
            ->method('stop')
            ->with($this->identicalTo($startedRequest))
            ->will($this->returnValue($stoppedRequest = $this->createRequestMock()));

        $this->formatter
            ->expects($this->once())
            ->method('formatRequest')
            ->with($this->identicalTo($stoppedRequest))
            ->will($this->returnValue($formattedRequest = 'request'));

        $this->formatter
            ->expects($this->once())
            ->method('formatResponse')
            ->with($this->identicalTo($response = $this->createResponseMock()))
            ->will($this->returnValue($formattedResponse = 'response'));

        $this->formatter
            ->expects($this->once())
            ->method('formatException')
            ->with($this->identicalTo($exception = $this->createExceptionMock($startedRequest, $response)))
            ->will($this->returnValue($formattedException = 'exception'));

        $exception
            ->expects($this->once())
            ->method('setRequest')
            ->with($this->identicalTo($stoppedRequest));

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with(
                $this->identicalTo('Unable to send "GET http://egeloen.fr".'),
                $this->identicalTo(array(
                    'adapter'   => 'http_adapter',
                    'exception' => $formattedException,
                    'request'   => $formattedRequest,
                    'response'  => $formattedResponse,
                ))
            );

        $this->loggerSubscriber->onRequestCreated($event = $this->createRequestCreatedEvent(null, $request));
        $this->loggerSubscriber->onRequestErrored($this->createRequestErroredEvent(null, $exception));

        $this->assertSame($startedRequest, $event->getRequest());
    }

    public function testMultiRequestSentEvent()
    {
        $requests = array($request1 = $this->createRequestMock(), $request2 = $this->createRequestMock());

        $this->timer
            ->expects($this->exactly(count($requests)))
            ->method('start')
            ->will($this->returnValueMap(array(
                array($request1, $startedRequest1 = $this->createRequestMock()),
                array($request2, $startedRequest2 = $this->createRequestMock()),
            )));

        $responses = array(
            $response1 = $this->createResponseMock($startedRequest1),
            $response2 = $this->createResponseMock($startedRequest2),
        );

        $this->timer
            ->expects($this->exactly(count($responses)))
            ->method('stop')
            ->will($this->returnValueMap(array(
                array($startedRequest1, $stoppedRequest1 = $this->createRequestMock()),
                array($startedRequest2, $stoppedRequest2 = $this->createRequestMock()),
            )));

        $this->formatter
            ->expects($this->exactly(count($responses)))
            ->method('formatRequest')
            ->will($this->returnValueMap(array(
                array($stoppedRequest1, $formattedRequest1 = 'request1'),
                array($stoppedRequest2, $formattedRequest2 = 'request2'),
            )));

        $this->formatter
            ->expects($this->exactly(count($responses)))
            ->method('formatResponse')
            ->will($this->returnValueMap(array(
                array($response1, $formattedResponse1 = 'response1'),
                array($response2, $formattedResponse2 = 'response2'),
            )));

        $this->logger
            ->expects($this->exactly(count($responses)))
            ->method('debug')
            ->withConsecutive(
                array(
                    $this->matchesRegularExpression('/^Send "GET http:\/\/egeloen\.fr" in [0-9]+\.[0-9]{2} ms\.$/'),
                    array(
                        'adapter'  => 'http_adapter',
                        'request'  => $formattedRequest1,
                        'response' => $formattedResponse1,
                    ),
                ),
                array(
                    $this->matchesRegularExpression('/^Send "GET http:\/\/egeloen\.fr" in [0-9]+\.[0-9]{2} ms\.$/'),
                    array(
                        'adapter'  => 'http_adapter',
                        'request'  => $formattedRequest2,
                        'response' => $formattedResponse2,
                    ),
                )
            );

        $response1
            ->expects($this->once())
            ->method('withParameter')
            ->with($this->identicalTo('request'), $this->identicalTo($stoppedRequest1))
            ->will($this->returnValue($stoppedResponse1 = $this->createResponseMock($stoppedRequest1)));

        $response2
            ->expects($this->once())
            ->method('withParameter')
            ->with($this->identicalTo('request'), $this->identicalTo($stoppedRequest2))
            ->will($this->returnValue($stoppedResponse2 = $this->createResponseMock($stoppedRequest2)));

        $this->loggerSubscriber->onMultiRequestCreated($requestCreatedEvent = $this->createMultiRequestCreatedEvent(null, $requests));
        $this->loggerSubscriber->onMultiRequestSent($requestSentEvent = $this->createMultiRequestSentEvent(null, $responses));

        $this->assertSame(array($startedRequest1, $startedRequest2), $requestCreatedEvent->getRequests());
        $this->assertSame(array($stoppedResponse1, $stoppedResponse2), $requestSentEvent->getResponses());
    }

    public function testMultiRequestErroredEvent()
    {
        $requests = array(
            $request1 = $this->createRequestMock(),
            $request2 = $this->createRequestMock(),
        );

        $this->timer
            ->expects($this->exactly(count($requests)))
            ->method('start')
            ->will($this->returnValueMap(array(
                array($request1, $startedRequest1 = $this->createRequestMock()),
                array($request2, $startedRequest2 = $this->createRequestMock()),
            )));

        $exceptions = array(
            $exception1 = $this->createExceptionMock(
                $startedRequest1,
                $response1 = $this->createResponseMock($request1)
            ),
            $exception2 = $this->createExceptionMock(
                $startedRequest2,
                $response2 = $this->createResponseMock($request2)
            ),
        );

        $this->timer
            ->expects($this->exactly(count($exceptions)))
            ->method('stop')
            ->will($this->returnValueMap(array(
                array($startedRequest1, $stoppedRequest1 = $this->createRequestMock()),
                array($startedRequest2, $stoppedRequest2 = $this->createRequestMock()),
            )));

        $this->formatter
            ->expects($this->exactly(count($exceptions)))
            ->method('formatRequest')
            ->will($this->returnValueMap(array(
                array($stoppedRequest1, $formattedRequest1 = 'request1'),
                array($stoppedRequest2, $formattedRequest2 = 'request2'),
            )));

        $this->formatter
            ->expects($this->exactly(count($exceptions)))
            ->method('formatResponse')
            ->will($this->returnValueMap(array(
                array($response1, $formattedResponse1 = 'response1'),
                array($response2, $formattedResponse2 = 'response2'),
            )));

        $this->formatter
            ->expects($this->exactly(count($exceptions)))
            ->method('formatException')
            ->will($this->returnValueMap(array(
                array($exception1, $formattedException1 = 'exception1'),
                array($exception2, $formattedException2 = 'exception1'),
            )));

        $this->logger
            ->expects($this->exactly(count($exceptions)))
            ->method('error')
            ->withConsecutive(
                array(
                    $this->identicalTo('Unable to send "GET http://egeloen.fr".'),
                    $this->identicalTo(array(
                        'adapter'   => 'http_adapter',
                        'exception' => $formattedException1,
                        'request'   => $formattedRequest1,
                        'response'  => $formattedResponse1,
                    )),
                ),
                array(
                    $this->identicalTo('Unable to send "GET http://egeloen.fr".'),
                    $this->identicalTo(array(
                        'adapter'   => 'http_adapter',
                        'exception' => $formattedException2,
                        'request'   => $formattedRequest2,
                        'response'  => $formattedResponse2,
                    )),
                )
            );

        $exception1
            ->expects($this->once())
            ->method('setRequest')
            ->with($this->identicalTo($stoppedRequest1));

        $exception2
            ->expects($this->once())
            ->method('setRequest')
            ->with($this->identicalTo($stoppedRequest2));

        $this->loggerSubscriber->onMultiRequestCreated($event = $this->createMultiRequestCreatedEvent(null, $requests));
        $this->loggerSubscriber->onMultiResponseErrored($this->createMultiRequestErroredEvent(null, $exceptions));

        $this->assertSame(array($startedRequest1, $startedRequest2), $event->getRequests());
    }

    /**
     * {@inheritdoc}
     */
    protected function createRequestMock()
    {
        $request = parent::createRequestMock();

        $request
            ->expects($this->any())
            ->method('getUri')
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
     * {@inheritdoc}
     */
    protected function createResponseMock(InternalRequestInterface $internalRequest = null)
    {
        $response = parent::createResponseMock();
        $response
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo('request'))
            ->will($this->returnValue($internalRequest));

        return $response;
    }

    /**
     * Creates a logger mock.
     *
     * @return \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject The logger mock.
     */
    private function createLoggerMock()
    {
        return $this->createMock('Psr\Log\LoggerInterface');
    }

    /**
     * Creates a formatter mock.
     *
     * @return \Ivory\HttpAdapter\Event\Formatter\FormatterInterface|\PHPUnit_Framework_MockObject_MockObject The formatter mock.
     */
    private function createFormatterMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Event\Formatter\FormatterInterface');
    }

    /**
     * Creates a timer mock.
     *
     * @return \Ivory\HttpAdapter\Event\Timer\TimerInterface|\PHPUnit_Framework_MockObject_MockObject The timer mock.
     */
    private function createTimerMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Event\Timer\TimerInterface');
    }
}
