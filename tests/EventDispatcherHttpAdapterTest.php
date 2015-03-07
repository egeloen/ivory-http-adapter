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
use Ivory\HttpAdapter\Event\ExceptionEvent;
use Ivory\HttpAdapter\Event\MultiExceptionEvent;
use Ivory\HttpAdapter\Event\MultiPostSendEvent;
use Ivory\HttpAdapter\Event\MultiPreSendEvent;
use Ivory\HttpAdapter\Event\PostSendEvent;
use Ivory\HttpAdapter\Event\PreSendEvent;
use Ivory\HttpAdapter\EventDispatcherHttpAdapter;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\MultiHttpAdapterException;

/**
 * Event dispatcher http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class EventDispatcherHttpAdapterTest extends \PHPUnit_Framework_TestCase
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

    public function testSendRequestDispatchPreSendEvent()
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
                $this->identicalTo(Events::PRE_SEND),
                $this->callback(function ($event) use ($httpAdapter, $internalRequest, $internalRequestOverride) {
                    $result =  $event instanceof PreSendEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getRequest() === $internalRequest;

                    $event->setRequest($internalRequestOverride);

                    return $result;
                })
            );

        $this->assertSame($response, $this->eventDispatcherHttpAdapter->sendRequest($internalRequest));
    }

    public function testSendRequestDispatchPostSendEvent()
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
                $this->identicalTo(Events::POST_SEND),
                $this->callback(function ($event) use ($httpAdapter, $internalRequest, $response, $responseOverride) {
                    $result = $event instanceof PostSendEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getRequest() === $internalRequest
                        && $event->getResponse() === $response;

                    $event->setResponse($responseOverride);

                    return $result;
                })
            );

        $this->assertSame($responseOverride, $this->eventDispatcherHttpAdapter->sendRequest($internalRequest));
    }

    public function testSendRequestDispatchExceptionEventAndReturnResponse()
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
                $this->identicalTo(Events::EXCEPTION),
                $this->callback(function ($event) use ($httpAdapter, $exception, $response) {
                    $result = $event instanceof ExceptionEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getException() === $exception;

                    $event->setResponse($response);

                    return $result;
                })
            );

        $this->assertSame($response, $this->eventDispatcherHttpAdapter->sendRequest($internalRequest));
    }

    public function testSendRequestDispatchExceptionEventWhenDoSendThrowException()
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
                $this->identicalTo(Events::EXCEPTION),
                $this->callback(function ($event) use ($httpAdapter, $exception, $exceptionOverride) {
                    $result = $event instanceof ExceptionEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getException() === $exception;

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

    public function testSendRequestDispatchExceptionEventWhenPostSendEventHasException()
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
                $this->identicalTo(Events::POST_SEND),
                $this->callback(function ($event) use ($exception) {
                    $event->setException($exception);

                    return true;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::EXCEPTION),
                $this->callback(function ($event) use ($httpAdapter, $exception) {
                    return $event instanceof ExceptionEvent
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

    public function testSendRequestsDispatchMultiPreSendEvent()
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
                $this->identicalTo(Events::MULTI_PRE_SEND),
                $this->callback(function ($event) use ($httpAdapter, $internalRequests, $internalRequestsOverride) {
                    $result =  $event instanceof MultiPreSendEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getRequests() === $internalRequests;

                    $event->setRequests($internalRequestsOverride);

                    return $result;
                })
            );

        $this->assertSame($responses, $this->eventDispatcherHttpAdapter->sendRequests($internalRequests));
    }

    public function testSendRequestsDispatchMultiPostSendEvent()
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
                $this->identicalTo(Events::MULTI_POST_SEND),
                $this->callback(function ($event) use ($httpAdapter, $responses, $responsesOverride) {
                    $result = $event instanceof MultiPostSendEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getResponses() === $responses
                        && !$event->hasExceptions();

                    $event->setResponses($responsesOverride);

                    return $result;
                })
            );

        $this->assertSame($responsesOverride, $this->eventDispatcherHttpAdapter->sendRequests($internalRequests));
    }

    public function testSendRequestsDispatchMultiExceptionEventAndReturnResponses()
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
                $this->identicalTo(Events::MULTI_EXCEPTION),
                $this->callback(function ($event) use ($httpAdapter, $exceptions, $responses) {
                    $result = $event instanceof MultiExceptionEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getExceptions() === $exceptions
                        && !$event->hasResponses();

                    $event->setExceptions(array());
                    $event->setResponses($responses);

                    return $result;
                })
            );

        $this->assertSame($responses, $this->eventDispatcherHttpAdapter->sendRequests($internalRequests));
    }

    public function testSendRequestsDispatchMultiExceptionEventWhenDoSendThrowException()
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
                $this->identicalTo(Events::MULTI_EXCEPTION),
                $this->callback(function ($event) use ($httpAdapter, $exceptions, $exceptionsOverride) {
                    $result = $event instanceof MultiExceptionEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getExceptions() === $exceptions
                        && !$event->hasResponses();

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

    public function testSendRequestsDispatchMultiExceptionEventWhenMultiPostSendEventHasExceptions()
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
                $this->identicalTo(Events::MULTI_POST_SEND),
                $this->callback(function ($event) use ($exceptions) {
                    $event->setExceptions($exceptions);

                    return true;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::MULTI_EXCEPTION),
                $this->callback(function ($event) use ($httpAdapter, $exceptions) {
                    return $event instanceof MultiExceptionEvent
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
        return $this->getMock('Ivory\HttpAdapter\PsrHttpAdapterInterface');
    }

    /**
     * Creates an event dispatcher mock.
     *
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject The event dispatcher mock.
     */
    private function createEventDispatcherMock()
    {
        return $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
    }

    /**
     * Creates an internal request mock.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject The internal request mock.
     */
    private function createInternalRequestMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
    }

    /**
     * Creates a response mock.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject The response mock.
     */
    private function createResponseMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }

    /**
     * Creates an exception mock.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException|\PHPUnit_Framework_MockObject_MockObject The exception mock.
     */
    private function createExceptionMock()
    {
        return $this->getMock('Ivory\HttpAdapter\HttpAdapterException');
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
        $exception = $this->getMock('Ivory\HttpAdapter\MultiHttpAdapterException');

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
