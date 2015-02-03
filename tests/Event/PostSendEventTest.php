<?php

/**
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event;

use Ivory\HttpAdapter\Event\PostSendEvent;

/**
 * Post send event test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PostSendEventTest extends PreSendEventTest
{
    /** @var \Ivory\HttpAdapter\Message\ResponseInterface[\PHPUnit_Framework_MockObject_MockObject */
    private $response;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->response = $this->createResponseMock();

        parent::setUp();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->response);

        parent::tearDown();
    }

    public function testDefaultState()
    {
        parent::testDefaultState();

        $this->assertSame($this->response, $this->event->getResponse());
        $this->assertFalse($this->event->hasException());
        $this->assertNull($this->event->getException());
    }

    public function testSetResponse()
    {
        $this->event->setResponse($response = $this->createResponseMock());

        $this->assertSame($response, $this->event->getResponse());
    }

    public function testSetException()
    {
        $this->event->setException($exception = $this->createExceptionMock());

        $this->assertTrue($this->event->hasException());
        $this->assertSame($exception, $this->event->getException());
    }

    /**
     * {@inheritdoc}
     */
    protected function createEvent()
    {
        return new PostSendEvent($this->httpAdapter, $this->request, $this->response);
    }

    /**
     * Creates a response mock.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface[\PHPUnit_Framework_MockObject_MockObject The response mock.
     */
    private function createResponseMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }

    /**
     * Creates an exception mock.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException[\PHPUnit_Framework_MockObject_MockObject The exception mock.
     */
    private function createExceptionMock()
    {
        return $this->getMock('Ivory\HttpAdapter\HttpAdapterException');
    }
}
