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
use Ivory\HttpAdapter\Event\PostSendEvent;
use Ivory\HttpAdapter\Event\PreSendEvent;
use Ivory\HttpAdapter\HttpAdapterException;

/**
 * Http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class HttpAdapterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\AbstractHttpAdapter|\PHPUnit_Framework_MockObject_MockObject */
    protected $httpAdapter;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->httpAdapter = $this->createHttpAdapterMockBuilder()->getMockForAbstractClass();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->httpAdapter);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Configuration', $this->httpAdapter->getConfiguration());
    }

    public function testInitialState()
    {
        $this->httpAdapter = $this->createHttpAdapterMockBuilder()
            ->setConstructorArgs(array($configuration = $this->createConfigurationMock()))
            ->getMockForAbstractClass();

        $this->assertSame($configuration, $this->httpAdapter->getConfiguration());
    }

    public function testSetConfiguration()
    {
        $this->httpAdapter->setConfiguration($configuration = $this->createConfigurationMock());

        $this->assertSame($configuration, $this->httpAdapter->getConfiguration());
    }

    public function testSendInternalRequestDispatchPreSendEvent()
    {
        $httpAdapter = $this->httpAdapter;
        $internalRequest = $this->createInternalRequestMock();
        $internalRequestOverride = $this->createInternalRequestMock();
        $response = $this->createResponseMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSend')
            ->with($this->identicalTo($internalRequestOverride))
            ->will($this->returnValue($response));

        $this->httpAdapter->getConfiguration()->setEventDispatcher(
            $eventDispatcher = $this->createEventDispatcherMock()
        );

        $eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::PRE_SEND),
                $this->callback(function ($event) use ($httpAdapter, $internalRequest, $internalRequestOverride) {
                    $result = $event instanceof PreSendEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getRequest() === $internalRequest;

                    $event->setRequest($internalRequestOverride);

                    return $result;
                })
            );

        $this->assertSame($response, $this->httpAdapter->sendInternalRequest($internalRequest));
    }

    public function testSendInternalRequestDispatchPostSendEvent()
    {
        $httpAdapter = $this->httpAdapter;
        $internalRequest = $this->createInternalRequestMock();
        $response = $this->createResponseMock();
        $responseOverride = $this->createResponseMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSend')
            ->with($this->identicalTo($internalRequest))
            ->will($this->returnValue($response));

        $this->httpAdapter->getConfiguration()->setEventDispatcher(
            $eventDispatcher = $this->createEventDispatcherMock()
        );

        $eventDispatcher
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

        $this->assertSame($responseOverride, $this->httpAdapter->sendInternalRequest($internalRequest));
    }

    public function testSendInternalRequestDispatchExceptionEventAndReturnResponse()
    {
        $httpAdapter = $this->httpAdapter;
        $internalRequest = $this->createInternalRequestMock();
        $exception = $this->createExceptionMock();
        $response = $this->createResponseMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSend')
            ->with($this->identicalTo($internalRequest))
            ->will($this->throwException($exception));

        $this->httpAdapter->getConfiguration()->setEventDispatcher(
            $eventDispatcher = $this->createEventDispatcherMock()
        );

        $eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::EXCEPTION),
                $this->callback(function ($event) use ($httpAdapter, $internalRequest, $exception, $response) {
                    $result = $event instanceof ExceptionEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getRequest() === $internalRequest
                        && $event->getException() === $exception;

                    $event->setResponse($response);

                    return $result;
                })
            );

        $this->assertSame($response, $this->httpAdapter->sendInternalRequest($internalRequest));
    }

    public function testSendInternalRequestDispatchExceptionEventWhenDoSendThrowException()
    {
        $httpAdapter = $this->httpAdapter;
        $internalRequest = $this->createInternalRequestMock();
        $exception = $this->createExceptionMock();
        $exceptionOverride = $this->createExceptionMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSend')
            ->with($this->identicalTo($internalRequest))
            ->will($this->throwException($exception));

        $this->httpAdapter->getConfiguration()->setEventDispatcher(
            $eventDispatcher = $this->createEventDispatcherMock()
        );

        $eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::EXCEPTION),
                $this->callback(function ($event) use ($httpAdapter, $internalRequest, $exception, $exceptionOverride) {
                    $result = $event instanceof ExceptionEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getRequest() === $internalRequest
                        && $event->getException() === $exception;

                    $event->setException($exceptionOverride);

                    return $result;
                })
            );

        $exceptionOverride
            ->expects($this->once())
            ->method('setRequest')
            ->with($this->identicalTo($internalRequest));

        $exceptionOverride
            ->expects($this->never())
            ->method('setResponse');

        try {
            $this->httpAdapter->sendInternalRequest($internalRequest);
            $this->fail();
        } catch (HttpAdapterException $e) {
            $this->assertSame($e, $exceptionOverride);
        }
    }

    public function testSendInternalRequestDispatchExceptionEventWhenPreSendThrowException()
    {
        $httpAdapter = $this->httpAdapter;
        $internalRequest = $this->createInternalRequestMock();
        $exception = $this->createExceptionMock();

        $this->httpAdapter->getConfiguration()->setEventDispatcher(
            $eventDispatcher = $this->createEventDispatcherMock()
        );

        $eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with($this->identicalTo(Events::PRE_SEND), $this->anything())
            ->will($this->throwException($exception));

        $eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::EXCEPTION),
                $this->callback(function ($event) use ($httpAdapter, $internalRequest, $exception) {
                    return $event instanceof ExceptionEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getRequest() === $internalRequest
                        && $event->getException() === $exception;
                })
            );

        $exception
            ->expects($this->once())
            ->method('setRequest')
            ->with($this->identicalTo($internalRequest));

        $exception
            ->expects($this->never())
            ->method('setResponse');

        try {
            $this->httpAdapter->sendInternalRequest($internalRequest);
            $this->fail();
        } catch (HttpAdapterException $e) {
            $this->assertSame($e, $exception);
        }
    }

    public function testSendInternalRequestDispatchExceptionEventWhenPostSendThrowException()
    {
        $httpAdapter = $this->httpAdapter;
        $internalRequest = $this->createInternalRequestMock();
        $response = $this->createResponseMock();
        $exception = $this->createExceptionMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSend')
            ->with($this->identicalTo($internalRequest))
            ->will($this->returnValue($response));

        $this->httpAdapter->getConfiguration()->setEventDispatcher(
            $eventDispatcher = $this->createEventDispatcherMock()
        );

        $eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with($this->identicalTo(Events::POST_SEND), $this->anything())
            ->will($this->throwException($exception));

        $eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::EXCEPTION),
                $this->callback(function ($event) use ($httpAdapter, $internalRequest, $exception) {
                    return $event instanceof ExceptionEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getRequest() === $internalRequest
                        && $event->getException() === $exception;
                })
            );

        $exception
            ->expects($this->once())
            ->method('setRequest')
            ->with($this->identicalTo($internalRequest));

        $exception
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->identicalTo($response));

        try {
            $this->httpAdapter->sendInternalRequest($internalRequest);
            $this->fail();
        } catch (HttpAdapterException $e) {
            $this->assertSame($e, $exception);
        }
    }

    /**
     * Creates an http adapter mock builder.
     *
     * @return \PHPUnit_Framework_MockObject_MockBuilder The http adapter mock builder.
     */
    protected function createHttpAdapterMockBuilder()
    {
        return $this->getMockBuilder('Ivory\HttpAdapter\AbstractHttpAdapter');
    }

    /**
     * Creates a message factory mock.
     *
     * @return \Ivory\HttpAdapter\Message\MessageFactoryInterface|\PHPUnit_Framework_MockObject_MockObject The message factory mock.
     */
    protected function createMessageFactoryMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\MessageFactoryInterface');
    }

    /**
     * Creates a configuration mock.
     *
     * @return \Ivory\HttpAdapter\ConfigurationInterface|\PHPUnit_Framework_MockObject_MockObject The configuration mock.
     */
    protected function createConfigurationMock()
    {
        return $this->getMock('Ivory\HttpAdapter\ConfigurationInterface');
    }

    /**
     * Creates an event dispatcher mock.
     *
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject The event dispatcher mock.
     */
    protected function createEventDispatcherMock()
    {
        return $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
    }

    /**
     * Creates an internal request mock.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject The internal request mock.
     */
    protected function createInternalRequestMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
    }

    /**
     * Creates a response mock.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject The response mock.
     */
    protected function createResponseMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }

    /**
     * Creates an exception mock.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException|\PHPUnit_Framework_MockObject_MockObject The exception mock.
     */
    protected function createExceptionMock()
    {
        return $this->getMock('Ivory\HttpAdapter\HttpAdapterException');
    }
}
