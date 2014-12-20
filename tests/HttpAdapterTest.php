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
use Ivory\HttpAdapter\HttpAdapterInterface;

/**
 * Http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class HttpAdapterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\AbstractHttpAdapter|\PHPUnit_Framework_MockObject_MockObject */
    private $httpAdapter;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->httpAdapter = $this->createHttpAdapterMockBuilder()->getMockForAbstractClass();
    }

    public function testVersion()
    {
        $this->assertRegExp('/\d+\.\d+\.\d+(-(.?))?/', HttpAdapterInterface::VERSION);
    }

    public function testVersionId()
    {
        $this->assertRegExp('/\d+\d+\d+\d+\d+/', HttpAdapterInterface::VERSION_ID);
    }

    public function testMajorVersion()
    {
        $this->assertRegExp('/\d+/', HttpAdapterInterface::MAJOR_VERSION);
    }

    public function testMinorVersion()
    {
        $this->assertRegExp('/\d+/', HttpAdapterInterface::MINOR_VERSION);
    }

    public function testPatchVersion()
    {
        $this->assertRegExp('/\d+/', HttpAdapterInterface::PATCH_VERSION);
    }

    public function testExtraVersion()
    {
        $this->assertRegExp('/.?/', HttpAdapterInterface::EXTRA_VERSION);
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

    public function testSendRequestWithDisabledEventDispatcher()
    {
        $this->httpAdapter
            ->expects($this->once())
            ->method('doSendInternalRequest')
            ->with($this->identicalTo($internalRequest = $this->createInternalRequestMock()))
            ->will($this->returnValue($response = $this->createResponseMock()));

        $this->httpAdapter->getConfiguration()->setEventDispatcher(null);

        $this->assertSame($response, $this->httpAdapter->sendRequest($internalRequest));
    }

    public function testSendRequestWithDisabledEventDispatcherThrowException()
    {
        $this->httpAdapter
            ->expects($this->once())
            ->method('doSendInternalRequest')
            ->with($this->identicalTo($internalRequest = $this->createInternalRequestMock()))
            ->will($this->throwException($exception = $this->createExceptionMock()));

        $this->httpAdapter->getConfiguration()->setEventDispatcher(null);

        try {
            $this->httpAdapter->sendRequest($internalRequest);
            $this->fail();
        } catch (HttpAdapterException $e) {
            $this->assertSame($e, $exception);
        }
    }

    public function testSendRequestDispatchPreSendEvent()
    {
        $httpAdapter = $this->httpAdapter;
        $internalRequest = $this->createInternalRequestMock();
        $internalRequestOverride = $this->createInternalRequestMock();
        $response = $this->createResponseMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSendInternalRequest')
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
                    $result =  $event instanceof PreSendEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getRequest() === $internalRequest;

                    $event->setRequest($internalRequestOverride);

                    return $result;
                })
            );

        $this->assertSame($response, $this->httpAdapter->sendRequest($internalRequest));
    }

    public function testSendRequestDispatchPostSendEvent()
    {
        $httpAdapter = $this->httpAdapter;
        $internalRequest = $this->createInternalRequestMock();
        $response = $this->createResponseMock();
        $responseOverride = $this->createResponseMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSendInternalRequest')
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

        $this->assertSame($responseOverride, $this->httpAdapter->sendRequest($internalRequest));
    }

    public function testSendRequestDispatchExceptionEventAndReturnResponse()
    {
        $httpAdapter = $this->httpAdapter;
        $internalRequest = $this->createInternalRequestMock();
        $exception = $this->createExceptionMock();
        $response = $this->createResponseMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSendInternalRequest')
            ->with($this->identicalTo($internalRequest))
            ->will($this->throwException($exception));

        $exception
            ->expects($this->once())
            ->method('setRequest')
            ->with($this->identicalTo($internalRequest));

        $exception
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->isNull());

        $this->httpAdapter->getConfiguration()->setEventDispatcher(
            $eventDispatcher = $this->createEventDispatcherMock()
        );

        $eventDispatcher
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

        $this->assertSame($response, $this->httpAdapter->sendRequest($internalRequest));
    }

    public function testSendRequestDispatchExceptionEventWhenDoSendThrowException()
    {
        $httpAdapter = $this->httpAdapter;
        $internalRequest = $this->createInternalRequestMock();
        $exception = $this->createExceptionMock();
        $exceptionOverride = $this->createExceptionMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSendInternalRequest')
            ->with($this->identicalTo($internalRequest))
            ->will($this->throwException($exception));

        $exception
            ->expects($this->once())
            ->method('setRequest')
            ->with($this->identicalTo($internalRequest));

        $exception
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->isNull());

        $this->httpAdapter->getConfiguration()->setEventDispatcher(
            $eventDispatcher = $this->createEventDispatcherMock()
        );

        $eventDispatcher
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
            $this->httpAdapter->sendRequest($internalRequest);
            $this->fail();
        } catch (HttpAdapterException $e) {
            $this->assertSame($e, $exceptionOverride);
        }
    }

    public function testSendRequestDispatchExceptionEventWhenPreSendThrowException()
    {
        $httpAdapter = $this->httpAdapter;
        $internalRequest = $this->createInternalRequestMock();
        $exception = $this->createExceptionMock();

        $exception
            ->expects($this->once())
            ->method('setRequest')
            ->with($this->identicalTo($internalRequest));

        $exception
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->isNull());

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
                $this->callback(function ($event) use ($httpAdapter, $exception) {
                    return $event instanceof ExceptionEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getException() === $exception;
                })
            );

        try {
            $this->httpAdapter->sendRequest($internalRequest);
            $this->fail();
        } catch (HttpAdapterException $e) {
            $this->assertSame($e, $exception);
        }
    }

    public function testSendRequestDispatchExceptionEventWhenPostSendThrowException()
    {
        $httpAdapter = $this->httpAdapter;
        $internalRequest = $this->createInternalRequestMock();
        $response = $this->createResponseMock();
        $exception = $this->createExceptionMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSendInternalRequest')
            ->with($this->identicalTo($internalRequest))
            ->will($this->returnValue($response));

        $exception
            ->expects($this->once())
            ->method('setRequest')
            ->with($this->identicalTo($internalRequest));

        $exception
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->identicalTo($response));

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
                $this->callback(function ($event) use ($httpAdapter, $exception) {
                    return $event instanceof ExceptionEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getException() === $exception;
                })
            );

        try {
            $this->httpAdapter->sendRequest($internalRequest);
            $this->fail();
        } catch (HttpAdapterException $e) {
            $this->assertSame($e, $exception);
        }
    }

    public function testSendRequestDispatchExceptionEventWhenPostSendEventHasException()
    {
        $httpAdapter = $this->httpAdapter;
        $internalRequest = $this->createInternalRequestMock();
        $response = $this->createResponseMock();
        $exception = $this->createExceptionMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSendInternalRequest')
            ->with($this->identicalTo($internalRequest))
            ->will($this->returnValue($response));

        $exception
            ->expects($this->once())
            ->method('setRequest')
            ->with($this->identicalTo($internalRequest));

        $exception
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->identicalTo($response));

        $this->httpAdapter->getConfiguration()->setEventDispatcher(
            $eventDispatcher = $this->createEventDispatcherMock()
        );

        $eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::POST_SEND),
                $this->callback(function ($event) use ($exception) {
                    $event->setException($exception);

                    return true;
                })
            );

        $eventDispatcher
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
            $this->httpAdapter->sendRequest($internalRequest);
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
    private function createHttpAdapterMockBuilder()
    {
        return $this->getMockBuilder('Ivory\HttpAdapter\AbstractHttpAdapter');
    }

    /**
     * Creates a message factory mock.
     *
     * @return \Ivory\HttpAdapter\Message\MessageFactoryInterface|\PHPUnit_Framework_MockObject_MockObject The message factory mock.
     */
    private function createMessageFactoryMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\MessageFactoryInterface');
    }

    /**
     * Creates a configuration mock.
     *
     * @return \Ivory\HttpAdapter\ConfigurationInterface|\PHPUnit_Framework_MockObject_MockObject The configuration mock.
     */
    private function createConfigurationMock()
    {
        return $this->getMock('Ivory\HttpAdapter\ConfigurationInterface');
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
}
