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

use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Ivory\HttpAdapter\MockHttpAdapter;

/**
 * @author GeLo <geloen.eric@gmail.com>
 * @author Timothée Barray <tim@amicalement-web.net>
 */
class MockHttpAdapterTest extends AbstractTestCase
{
    /**
     * @var MockHttpAdapter
     */
    private $mockHttpAdapter;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->mockHttpAdapter = new MockHttpAdapter();
    }

    public function testDefaultState()
    {
        $this->assertEmpty($this->mockHttpAdapter->getQueuedResponses());
        $this->assertEmpty($this->mockHttpAdapter->getReceivedRequests());
    }

    public function testName()
    {
        $this->assertSame('mock', $this->mockHttpAdapter->getName());
    }

    public function testAppendResponse()
    {
        $response1 = $this->createResponseMock();
        $response2 = $this->createResponseMock();

        $this->mockHttpAdapter->appendResponse($response1);
        $this->mockHttpAdapter->appendResponse($response2);

        $this->assertSame([$response1, $response2], $this->mockHttpAdapter->getQueuedResponses());
    }

    public function testSendInternalRequest()
    {
        $internalRequest = $this->createInternalRequestMock();
        $response = $this->createResponseMock();
        $this->mockHttpAdapter->appendResponse($response);

        $this->assertSame($response, $this->mockHttpAdapter->sendRequest($internalRequest));
        $this->assertSame([$internalRequest], $this->mockHttpAdapter->getReceivedRequests());
        $this->assertEmpty($this->mockHttpAdapter->getQueuedResponses());
    }

    public function testReset()
    {
        $internalRequest = $this->createInternalRequestMock();
        $response1 = $this->createResponseMock();
        $response2 = $this->createResponseMock();

        $this->mockHttpAdapter->appendResponse($response1);
        $this->mockHttpAdapter->appendResponse($response2);

        $this->assertSame($response1, $this->mockHttpAdapter->sendRequest($internalRequest));
        $this->assertSame([$internalRequest], $this->mockHttpAdapter->getReceivedRequests());
        $this->assertSame([$response2], $this->mockHttpAdapter->getQueuedResponses());

        $this->mockHttpAdapter->reset();

        $this->assertEmpty($this->mockHttpAdapter->getQueuedResponses());
        $this->assertEmpty($this->mockHttpAdapter->getReceivedRequests());
    }

    /**
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage You must append a response in the queue before sending a request.
     */
    public function testSendInternalRequestWithNoQueuedResponse()
    {
        $this->mockHttpAdapter->get('http://google.com');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|InternalRequestInterface
     */
    private function createInternalRequestMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResponseInterface
     */
    private function createResponseMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }
}
