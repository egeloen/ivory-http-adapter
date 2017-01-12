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

use Ivory\HttpAdapter\HttpAdapterException;

/**
 * Http adapter exception test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class HttpAdapterExceptionTest extends AbstractTestCase
{
    /** @var \Ivory\HttpAdapter\HttpAdapterException */
    private $exception;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->exception = new HttpAdapterException();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->exception);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf('Exception', $this->exception);
        $this->assertFalse($this->exception->hasRequest());
        $this->assertFalse($this->exception->hasResponse());
    }

    public function testSetRequest()
    {
        $this->exception->setRequest($request = $this->createRequestMock());

        $this->assertTrue($this->exception->hasRequest());
        $this->assertSame($request, $this->exception->getRequest());
    }

    public function testResetRequest()
    {
        $this->exception->setRequest($this->createRequestMock());
        $this->exception->setRequest(null);

        $this->assertFalse($this->exception->hasRequest());
        $this->assertNull($this->exception->getRequest());
    }

    public function testSetResponse()
    {
        $this->exception->setResponse($request = $this->createResponseMock());

        $this->assertTrue($this->exception->hasResponse());
        $this->assertSame($request, $this->exception->getResponse());
    }

    public function testResetResponse()
    {
        $this->exception->setResponse($this->createResponseMock());
        $this->exception->setResponse(null);

        $this->assertFalse($this->exception->hasRequest());
        $this->assertNull($this->exception->getRequest());
    }

    /**
     * Creates a request mock.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject The request mock.
     */
    private function createRequestMock()
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
}
