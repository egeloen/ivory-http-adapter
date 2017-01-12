<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event;

use Ivory\HttpAdapter\Event\RequestErroredEvent;

/**
 * Exception event test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class RequestErroredEventTest extends AbstractEventTest
{
    /** @var \Ivory\HttpAdapter\HttpAdapterException|\PHPUnit_Framework_MockObject_MockObject */
    private $exception;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->exception = $this->createExceptionMock();

        parent::setUp();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->exception);

        parent::tearDown();
    }

    public function testDefaultState()
    {
        parent::testDefaultState();

        $this->assertSame($this->exception, $this->event->getException());
        $this->assertFalse($this->event->hasResponse());
    }

    public function testSetException()
    {
        $this->event->setException($exception = $this->createExceptionMock());

        $this->assertSame($exception, $this->event->getException());
    }

    public function testSetResponse()
    {
        $this->event->setResponse($response = $this->createResponseMock());

        $this->assertTrue($this->event->hasResponse());
        $this->assertSame($response, $this->event->getResponse());
    }

    /**
     * {@inheritdoc}
     */
    protected function createEvent()
    {
        return new RequestErroredEvent($this->httpAdapter, $this->exception);
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
     * Creates a response mock.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface[\PHPUnit_Framework_MockObject_MockObject The response mock.
     */
    private function createResponseMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }
}
