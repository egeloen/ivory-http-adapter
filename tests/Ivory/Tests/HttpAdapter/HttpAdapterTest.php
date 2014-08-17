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
use Ivory\HttpAdapter\HttpAdapterConfigInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;

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
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\MessageFactory', $this->httpAdapter->getMessageFactory());

        $this->assertInstanceOf(
            'Symfony\Component\EventDispatcher\EventDispatcher',
            $this->httpAdapter->getEventDispatcher()
        );

        $this->assertSame(InternalRequestInterface::PROTOCOL_VERSION_11, $this->httpAdapter->getProtocolVersion());
        $this->assertFalse($this->httpAdapter->getKeepAlive());
        $this->assertFalse($this->httpAdapter->hasEncodingType());
        $this->assertInternalType('string', $this->httpAdapter->getBoundary());
        $this->assertSame(10, $this->httpAdapter->getTimeout());
        $this->assertTrue($this->httpAdapter->hasMaxRedirects());
        $this->assertSame(5, $this->httpAdapter->getMaxRedirects());
    }

    public function testSetMessageFactory()
    {
        $this->httpAdapter->setMessageFactory($messageFactory = $this->createMessageFactoryMock());

        $this->assertSame($messageFactory, $this->httpAdapter->getMessageFactory());
    }

    public function testSetEventDispatcher()
    {
        $this->httpAdapter->setEventDispatcher($eventDispatcher = $this->createEventDispatcherMock());

        $this->assertSame($eventDispatcher, $this->httpAdapter->getEventDispatcher());
    }

    public function testSetProtocolVersion()
    {
        $this->httpAdapter->setProtocolVersion($protocolVersion = InternalRequestInterface::PROTOCOL_VERSION_10);

        $this->assertSame($protocolVersion, $this->httpAdapter->getProtocolVersion());
    }

    public function testSetKeepAlive()
    {
        $this->httpAdapter->setKeepAlive(true);

        $this->assertTrue($this->httpAdapter->getKeepAlive());
    }

    public function testSetEncodingType()
    {
        $this->httpAdapter->setEncodingType($encodingType = HttpAdapterConfigInterface::ENCODING_TYPE_FORMDATA);

        $this->assertSame($encodingType, $this->httpAdapter->getEncodingType());
    }

    public function testSetBoundary()
    {
        $this->httpAdapter->setBoundary($boundary = 'foo');

        $this->assertSame($boundary, $this->httpAdapter->getBoundary());
    }

    public function testSetTimeout()
    {
        $this->httpAdapter->setTimeout($timeout = 2.5);

        $this->assertSame($timeout, $this->httpAdapter->getTimeout());
    }

    public function testSetMaxRedirects()
    {
        $this->httpAdapter->setMaxRedirects($maxRedirects = 0);

        $this->assertFalse($this->httpAdapter->hasMaxRedirects());
        $this->assertSame($maxRedirects, $this->httpAdapter->getMaxRedirects());
    }

    public function testSendInternalRequestDispatchPreSendEvent()
    {
        $httpAdapter = $this->httpAdapter;
        $internalRequest = $this->createInternalRequestMock();
        $response = $this->createResponseMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSend')
            ->will($this->returnValue($response));

        $this->httpAdapter->setEventDispatcher($eventDispatcher = $this->createEventDispatcherMock());

        $eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::PRE_SEND),
                $this->callback(function ($event) use ($httpAdapter, $internalRequest) {
                    return $event instanceof PreSendEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getRequest() === $internalRequest;
                })
            );

        $this->httpAdapter->sendInternalRequest($internalRequest);
    }

    public function testSendInternalRequestDispatchPostSendEvent()
    {
        $httpAdapter = $this->httpAdapter;
        $internalRequest = $this->createInternalRequestMock();
        $response = $this->createResponseMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSend')
            ->will($this->returnValue($response));

        $this->httpAdapter->setEventDispatcher($eventDispatcher = $this->createEventDispatcherMock());

        $eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::POST_SEND),
                $this->callback(function ($event) use ($httpAdapter, $internalRequest, $response) {
                    return $event instanceof PostSendEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getRequest() === $internalRequest
                        && $event->getResponse() === $response;
                })
            );

        $this->httpAdapter->sendInternalRequest($internalRequest);
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testSendInternalRequestDispatchExceptionEvent()
    {
        $httpAdapter = $this->httpAdapter;
        $internalRequest = $this->createInternalRequestMock();
        $exception = $this->createExceptionMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSend')
            ->will($this->throwException($exception));

        $this->httpAdapter->setEventDispatcher($eventDispatcher = $this->createEventDispatcherMock());

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

        $this->httpAdapter->sendInternalRequest($internalRequest);
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
