<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\Retry;

use Ivory\HttpAdapter\Event\Retry\Retry;

/**
 * Retry test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class RetryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\Event\Retry\Retry */
    private $retry;

    /** @var \Ivory\HttpAdapter\Event\Retry\Strategy\RetryStrategyInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $strategy;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->retry = new Retry($this->strategy = $this->createStrategyMock());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->strategy);
        unset($this->retry);
    }

    public function testDefaultState()
    {
        $this->retry = new Retry();

        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Retry\Strategy\LimitedRetryStrategy',
            $strategy = $this->retry->getStrategy()
        );

        $this->assertSame(3, $strategy->getLimit());
        $this->assertTrue($strategy->hasNext());
        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Retry\Strategy\ExponentialDelayedRetryStrategy',
            $strategy->getNext()
        );
    }

    public function testInitialState()
    {
        $this->assertSame($this->strategy, $this->retry->getStrategy());
    }

    public function testSetStrategy()
    {
        $this->retry->setStrategy($strategy = $this->createStrategyMock());

        $this->assertSame($strategy, $this->retry->getStrategy());
    }

    public function testRetryWithStrategyNotVerified()
    {
        $this->strategy
            ->expects($this->once())
            ->method('verify')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue(false));

        $request
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo(Retry::RETRY_COUNT))
            ->will($this->returnValue($retryCount = null));

        $request
            ->expects($this->once())
            ->method('setParameter')
            ->with($this->identicalTo(Retry::RETRY_COUNT), $this->identicalTo((int) $retryCount));

        $this->assertNull($this->retry->retry($request, $this->createHttpAdapterMock()));
    }

    public function testRetryWithStrategyVerified()
    {
        $this->strategy
            ->expects($this->once())
            ->method('verify')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue(true));

        $request
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo(Retry::RETRY_COUNT))
            ->will($this->returnValue($retryCount = null));

        $request
            ->expects($this->once())
            ->method('setParameter')
            ->with($this->identicalTo(Retry::RETRY_COUNT), $this->identicalTo(++$retryCount));

        $httpAdapter = $this->createHttpAdapterMock();
        $httpAdapter
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->identicalTo($request))
            ->will($this->returnValue($response = $this->createResponseMock()));

        $before = microtime(true);
        $retryResponse = $this->retry->retry($request, $httpAdapter);
        $after = microtime(true);

        $this->assertLessThanOrEqual(0.1, $after - $before);
        $this->assertSame($retryResponse, $response);
    }

    public function testRetryWithStrategyDelayed()
    {
        $this->strategy
            ->expects($this->once())
            ->method('verify')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue(true));

        $this->strategy
            ->expects($this->once())
            ->method('delay')
            ->with($this->identicalTo($request))
            ->will($this->returnValue($delay = 0.5));

        $request
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo(Retry::RETRY_COUNT))
            ->will($this->returnValue($retryCount = 1));

        $request
            ->expects($this->once())
            ->method('setParameter')
            ->with($this->identicalTo(Retry::RETRY_COUNT), $this->identicalTo(++$retryCount));

        $httpAdapter = $this->createHttpAdapterMock();
        $httpAdapter
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->identicalTo($request))
            ->will($this->returnValue($response = $this->createResponseMock()));

        $before = microtime(true);
        $retryResponse = $this->retry->retry($request, $httpAdapter);
        $after = microtime(true);

        $this->assertGreaterThanOrEqual($delay, $after - $before);
        $this->assertLessThanOrEqual($delay + 0.1, $after - $before);
        $this->assertSame($retryResponse, $response);
    }

    /**
     * Creates a strategy mock.
     *
     * @return \Ivory\HttpAdapter\Event\Retry\Strategy\RetryStrategyInterface|\PHPUnit_Framework_MockObject_MockObject The strategy mock.
     */
    private function createStrategyMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Event\Retry\Strategy\RetryStrategyInterface');
    }

    /**
     * Creates a request mock.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject The request mock.
     */
    private function createRequestMock()
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
     * Creates an http adapter mock.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterInterface|\PHPUnit_Framework_MockObject_MockObject The http adapter mock.
     */
    private function createHttpAdapterMock()
    {
        return $this->getMock('Ivory\HttpAdapter\HttpAdapterInterface');
    }
}
