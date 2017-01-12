<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter;

use Ivory\HttpAdapter\Event\Events;
use Ivory\HttpAdapter\Event\RequestErroredEvent;
use Ivory\HttpAdapter\Event\MultiRequestErroredEvent;
use Ivory\HttpAdapter\Event\MultiRequestSentEvent;
use Ivory\HttpAdapter\Event\MultiRequestCreatedEvent;
use Ivory\HttpAdapter\Event\RequestSentEvent;
use Ivory\HttpAdapter\Event\RequestCreatedEvent;
use Ivory\HttpAdapter\EventDispatcherHttpAdapter;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\MultiHttpAdapterException;

/**
 * Event dispatcher http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class EventDispatcherHttpAdapterTest extends AbstractTestCase
{
    /** @var \Ivory\HttpAdapter\EventDispatcherHttpAdapter */
    private $eventDispatcherHttpAdapter;

    /** @var \Ivory\HttpAdapter\PsrHttpAdapterInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $httpAdapter;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $eventDispatcher;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->eventDispatcherHttpAdapter = new EventDispatcherHttpAdapter(
            $this->httpAdapter = $this->createHttpAdapterMock(),
            $this->eventDispatcher = $this->createEventDispatcherMock()
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->eventDispatcher);
        unset($this->httpAdapter);
        unset($this->eventDispatcherHttpAdapter);
    }

    public function testSendRequestDispatchRequestCreatedEvent()
    {
        $httpAdapter = $this->eventDispatcherHttpAdapter;
        $internalRequest = $this->createInternalRequestMock();
        $internalRequestOverride = $this->createInternalRequestMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->identicalTo($internalRequestOverride))
            ->will($this->returnValue($response = $this->createResponseMock()));

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::REQUEST_CREATED),
                $this->callback(function ($event) use ($httpAdapter, $internalRequest, $internalRequestOverride) {
                    static $result = null;

                    if ($result === null) {
                        $result = $event instanceof RequestCreatedEvent
                            && $event->getHttpAdapter() === $httpAdapter
                            && $event->getRequest() === $internalRequest;
                    }

                    $event->setRequest($internalRequestOverride);

                    return $result;
                })
            );

        $this->assertSame($response, $this->eventDispatcherHttpAdapter->sendRequest($internalRequest));
    }

    public function testSendRequestDispatchRequestCreatedEventAndReturnResponse()
    {
        $httpAdapter = $this->eventDispatcherHttpAdapter;
        $internalRequest = $this->createInternalRequestMock();
        $response = $this->createResponseMock();

        $this->httpAdapter
            ->expects($this->never())
            ->method('sendRequest');

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::REQUEST_CREATED),
                $this->callback(function ($event) use ($httpAdapter, $internalRequest, $response) {
                    $result =  $event instanceof RequestCreatedEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getRequest() === $internalRequest;

                    $event->setResponse($response);

                    return $result;
                })
            );

        $this->assertSame($response, $this->eventDispatcherHttpAdapter->sendRequest($internalRequest));
    }

    public function testSendRequestDispatchRequestCreatedEventAndThrowException()
    {
        $httpAdapter = $this->eventDispatcherHttpAdapter;
        $internalRequest = $this->createInternalRequestMock();
        $exception = $this->createExceptionMock();

        $this->httpAdapter
            ->expects($this->never())
            ->method('sendRequest');

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::REQUEST_CREATED),
                $this->callback(function ($event) use ($httpAdapter, $internalRequest, $exception) {
                    $result =  $event instanceof RequestCreatedEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getRequest() === $internalRequest;

                    $event->setException($exception);

                    return $result;
                })
            );

        try {
            $this->eventDispatcherHttpAdapter->sendRequest($internalRequest);
            $this->fail();
        } catch (HttpAdapterException $e) {
            $this->assertSame($e, $exception);
        }
    }

    public function testSendRequestDispatchRequestSentEvent()
    {
        $httpAdapter = $this->eventDispatcherHttpAdapter;
        $internalRequest = $this->createInternalRequestMock();
        $response = $this->createResponseMock();
        $responseOverride = $this->createResponseMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->identicalTo($internalRequest))
            ->will($this->returnValue($response));

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::REQUEST_SENT),
                $this->callback(function ($event) use ($httpAdapter, $internalRequest, $response, $responseOverride) {
                    static $result = null;

                    if ($result === null) {
                        $result = $event instanceof RequestSentEvent
                            && $event->getHttpAdapter() === $httpAdapter
                            && $event->getRequest() === $internalRequest
                            && $event->getResponse() === $response;
                    }

                    $event->setResponse($responseOverride);

                    return $result;
                })
            );

        $this->assertSame($responseOverride, $this->eventDispatcherHttpAdapter->sendRequest($internalRequest));
    }

    public function testSendRequestDispatchRequestErroredEventAndReturnResponse()
    {
        $httpAdapter = $this->eventDispatcherHttpAdapter;
        $response = $this->createResponseMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->identicalTo($internalRequest = $this->createInternalRequestMock()))
            ->will($this->throwException($exception = $this->createExceptionMock()));

        $exception
            ->expects($this->once())
            ->method('setRequest')
            ->with($this->identicalTo($internalRequest));

        $exception
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->isNull());

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::REQUEST_ERRORED),
                $this->callback(function ($event) use ($httpAdapter, $exception, $response) {
                    $result = $event instanceof RequestErroredEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getException() === $exception;

                    $event->setResponse($response);

                    return $result;
                })
            );

        $this->assertSame($response, $this->eventDispatcherHttpAdapter->sendRequest($internalRequest));
    }

    public function testSendRequestDispatchRequestErroredEventWhenDoSendThrowException()
    {
        $httpAdapter = $this->eventDispatcherHttpAdapter;
        $exceptionOverride = $this->createExceptionMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->identicalTo($internalRequest = $this->createInternalRequestMock()))
            ->will($this->throwException($exception = $this->createExceptionMock()));

        $exception
            ->expects($this->once())
            ->method('setRequest')
            ->with($this->identicalTo($internalRequest));

        $exception
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->isNull());

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::REQUEST_ERRORED),
                $this->callback(function ($event) use ($httpAdapter, $exception, $exceptionOverride) {
                    static $result = null;

                    if ($result === null) {
                        $result = $event instanceof RequestErroredEvent
                            && $event->getHttpAdapter() === $httpAdapter
                            && $event->getException() === $exception;
                    }

                    $event->setException($exceptionOverride);

                    return $result;
                })
            );

        $exception
            ->expects($this->once())
            ->method('setRequest')
            ->with($this->identicalTo($internalRequest));

        $exception
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->isNull());

        try {
            $this->eventDispatcherHttpAdapter->sendRequest($internalRequest);
            $this->fail();
        } catch (HttpAdapterException $e) {
            $this->assertSame($e, $exceptionOverride);
        }
    }

    public function testSendRequestDispatchRequestErroredEventWhenRequestSentEventHasException()
    {
        $httpAdapter = $this->eventDispatcherHttpAdapter;
        $exception = $this->createExceptionMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->identicalTo($internalRequest = $this->createInternalRequestMock()))
            ->will($this->returnValue($response = $this->createResponseMock()));

        $exception
            ->expects($this->once())
            ->method('setRequest')
            ->with($this->identicalTo($internalRequest));

        $exception
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->identicalTo($response));

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::REQUEST_SENT),
                $this->callback(function ($event) use ($exception) {
                    $event->setException($exception);

                    return true;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::REQUEST_ERRORED),
                $this->callback(function ($event) use ($httpAdapter, $exception) {
                    return $event instanceof RequestErroredEvent
                    && $event->getHttpAdapter() === $httpAdapter
                    && $event->getException() === $exception;
                })
            );

        try {
            $this->eventDispatcherHttpAdapter->sendRequest($internalRequest);
            $this->fail();
        } catch (HttpAdapterException $e) {
            $this->assertSame($e, $exception);
        }
    }

    public function testSendRequestsDispatchMultiRequestCreatedEvent()
    {
        $httpAdapter = $this->eventDispatcherHttpAdapter;
        $internalRequests = array($this->createInternalRequestMock());

        $this->httpAdapter
            ->expects($this->once())
            ->method('sendRequests')
            ->with($this->identicalTo($internalRequestsOverride = array(
                $internalRequestOverride = $this->createInternalRequestMock(), )
            ))
            ->will($this->returnValue($responses = array($response = $this->createResponseMock())));

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::MULTI_REQUEST_CREATED),
                $this->callback(function ($event) use ($httpAdapter, $internalRequests, $internalRequestsOverride) {
                    static $result = null;

                    if ($result === null) {
                        $result = $event instanceof MultiRequestCreatedEvent
                            && $event->getHttpAdapter() === $httpAdapter
                            && $event->getRequests() === $internalRequests;
                    }

                    $event->setRequests($internalRequestsOverride);

                    return $result;
                })
            );

        $this->assertSame($responses, $this->eventDispatcherHttpAdapter->sendRequests($internalRequests));
    }

    public function testSendRequestsDispatchMultiRequestSentEvent()
    {
        $httpAdapter = $this->eventDispatcherHttpAdapter;
        $responsesOverride = array($this->createResponseMock());

        $this->httpAdapter
            ->expects($this->once())
            ->method('sendRequests')
            ->with($this->identicalTo($internalRequests = array($this->createInternalRequestMock())))
            ->will($this->returnValue($responses = array($this->createResponseMock())));

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::MULTI_REQUEST_SENT),
                $this->callback(function ($event) use ($httpAdapter, $responses, $responsesOverride) {
                    static $result = null;

                    if ($result === null) {
                        $result = $event instanceof MultiRequestSentEvent
                            && $event->getHttpAdapter() === $httpAdapter
                            && $event->getResponses() === $responses
                            && !$event->hasExceptions();
                    }

                    $event->setResponses($responsesOverride);

                    return $result;
                })
            );

        $this->assertSame($responsesOverride, $this->eventDispatcherHttpAdapter->sendRequests($internalRequests));
    }

    public function testSendRequestsDispatchMultiRequestErroredEventAndReturnResponses()
    {
        $httpAdapter = $this->eventDispatcherHttpAdapter;
        $responses = array($this->createResponseMock());

        $this->httpAdapter
            ->expects($this->once())
            ->method('sendRequests')
            ->with($this->identicalTo($internalRequests = array($this->createInternalRequestMock())))
            ->will($this->throwException($this->createMultiExceptionMock(
                $exceptions = array($this->createExceptionMock())
            )));

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::MULTI_REQUEST_ERRORED),
                $this->callback(function ($event) use ($httpAdapter, $exceptions, $responses) {
                    static $result = null;

                    if ($result === null) {
                        $result = $event instanceof MultiRequestErroredEvent
                            && $event->getHttpAdapter() === $httpAdapter
                            && $event->getExceptions() === $exceptions
                            && !$event->hasResponses();
                    }

                    $event->setExceptions(array());
                    $event->setResponses($responses);

                    return $result;
                })
            );

        $this->assertSame($responses, $this->eventDispatcherHttpAdapter->sendRequests($internalRequests));
    }

    public function testSendRequestsDispatchMultiRequestErroredEventWhenDoSendThrowException()
    {
        $httpAdapter = $this->eventDispatcherHttpAdapter;
        $exceptionsOverride = array($this->createExceptionMock());

        $this->httpAdapter
            ->expects($this->once())
            ->method('sendRequests')
            ->with($this->identicalTo($internalRequests = array($this->createInternalRequestMock())))
            ->will($this->throwException($this->createMultiExceptionMock(
                $exceptions = array($this->createExceptionMock())
            )));

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::MULTI_REQUEST_ERRORED),
                $this->callback(function ($event) use ($httpAdapter, $exceptions, $exceptionsOverride) {
                    static $result = null;

                    if ($result === null) {
                        $result = $event instanceof MultiRequestErroredEvent
                            && $event->getHttpAdapter() === $httpAdapter
                            && $event->getExceptions() === $exceptions
                            && !$event->hasResponses();
                    }

                    $event->setExceptions($exceptionsOverride);

                    return $result;
                })
            );

        try {
            $this->eventDispatcherHttpAdapter->sendRequests($internalRequests);
            $this->fail();
        } catch (MultiHttpAdapterException $e) {
            $this->assertSame($e->getExceptions(), $exceptionsOverride);
            $this->assertFalse($e->hasResponses());
        }
    }

    public function testSendRequestsDispatchMultiRequestErroredEventWhenMultiRequestSentEventHasExceptions()
    {
        $httpAdapter = $this->eventDispatcherHttpAdapter;
        $exceptions = array($this->createExceptionMock());

        $this->httpAdapter
            ->expects($this->once())
            ->method('sendRequests')
            ->with($this->identicalTo($internalRequests = array($this->createInternalRequestMock())))
            ->will($this->returnValue($responses = array($this->createResponseMock())));

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::MULTI_REQUEST_SENT),
                $this->callback(function ($event) use ($exceptions) {
                    $event->setExceptions($exceptions);

                    return true;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::MULTI_REQUEST_ERRORED),
                $this->callback(function ($event) use ($httpAdapter, $exceptions) {
                    return $event instanceof MultiRequestErroredEvent
                    && $event->getHttpAdapter() === $httpAdapter
                    && $event->getExceptions() === $exceptions;
                })
            );

        try {
            $this->eventDispatcherHttpAdapter->sendRequests($internalRequests);
            $this->fail();
        } catch (MultiHttpAdapterException $e) {
            $this->assertSame($e->getExceptions(), $exceptions);
            $this->assertTrue($e->hasResponses(), $responses);
        }
    }

    /**
     * Creates an http adapter mock.
     *
     * @return \Ivory\HttpAdapter\PsrHttpAdapterInterface|\PHPUnit_Framework_MockObject_MockObject The http adapter mock.
     */
    private function createHttpAdapterMock()
    {
        return $this->createMock('Ivory\HttpAdapter\PsrHttpAdapterInterface');
    }

    /**
     * Creates an event dispatcher mock.
     *
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject The event dispatcher mock.
     */
    private function createEventDispatcherMock()
    {
        return $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
    }

    /**
     * Creates an internal request mock.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject The internal request mock.
     */
    private function createInternalRequestMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
    }

    /**
     * Creates a response mock.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject The response mock.
     */
    private function createResponseMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }

    /**
     * Creates an exception mock.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException|\PHPUnit_Framework_MockObject_MockObject The exception mock.
     */
    private function createExceptionMock()
    {
        return $this->createMock('Ivory\HttpAdapter\HttpAdapterException');
    }

    /**
     * Creates a multi exception mock.
     *
     * @param array $exceptions The exceptions.
     * @param array $responses  The responses.
     *
     * @return \Ivory\HttpAdapter\MultiHttpAdapterException|\PHPUnit_Framework_MockObject_MockObject The multi exception mock.
     */
    private function createMultiExceptionMock(array $exceptions = array(), array $responses = array())
    {
        $exception = $this->createMock('Ivory\HttpAdapter\MultiHttpAdapterException');

        if (empty($exceptions)) {
            $exceptions[] = $this->createExceptionMock();
        }

        $exception
            ->expects($this->any())
            ->method('hasExceptions')
            ->will($this->returnValue(true));

        $exception
            ->expects($this->any())
            ->method('getExceptions')
            ->will($this->returnValue($exceptions));

        $exception
            ->expects($this->any())
            ->method('hasResponses')
            ->will($this->returnValue(!empty($responses)));

        $exception
            ->expects($this->any())
            ->method('getResponses')
            ->will($this->returnValue($responses));

        return $exception;
    }
}
