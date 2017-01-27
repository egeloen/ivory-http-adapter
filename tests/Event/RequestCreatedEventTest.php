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

use Ivory\HttpAdapter\Event\RequestCreatedEvent;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RequestCreatedEventTest extends AbstractEventTest
{
    /**
     * @var InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->request = $this->createRequestMock();

        parent::setUp();
    }

    public function testDefaultState()
    {
        parent::setUp();

        $this->assertSame($this->request, $this->event->getRequest());
        $this->assertFalse($this->event->hasResponse());
        $this->assertNull($this->event->getResponse());
        $this->assertFalse($this->event->hasException());
        $this->assertNull($this->event->getException());
    }

    public function testSetRequest()
    {
        $this->event->setRequest($request = $this->createRequestMock());

        $this->assertSame($request, $this->event->getRequest());
    }

    public function testSetResponse()
    {
        $this->event->setResponse($response = $this->createResponseMock());

        $this->assertTrue($this->event->hasResponse());
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
        return new RequestCreatedEvent($this->httpAdapter, $this->request);
    }

    /**
     * @return InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createRequestMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
    }

    /**
     * @return ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createResponseMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }

    /**
     * @return HttpAdapterException|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createExceptionMock()
    {
        return $this->createMock('Ivory\HttpAdapter\HttpAdapterException');
    }
}
