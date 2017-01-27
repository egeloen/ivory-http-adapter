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
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class HttpAdapterExceptionTest extends AbstractTestCase
{
    /**
     * @var HttpAdapterException
     */
    private $exception;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->exception = new HttpAdapterException();
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
}
