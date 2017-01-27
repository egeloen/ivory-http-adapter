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
use Ivory\HttpAdapter\Event\Retry\Strategy\RetryStrategyInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RetryTest extends AbstractTestCase
{
    /**
     * @var Retry
     */
    private $retry;

    /**
     * @var RetryStrategyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $strategy;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->retry = new Retry($this->strategy = $this->createStrategyMock());
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
            ->expects($this->never())
            ->method('withParameter');

        $this->assertFalse($this->retry->retry($request));
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
            ->method('withParameter')
            ->with($this->identicalTo(Retry::RETRY_COUNT), $this->identicalTo(++$retryCount))
            ->will($this->returnValue($request));

        $before = microtime(true);
        $result = $this->retry->retry($request);
        $after = microtime(true);

        $this->assertLessThanOrEqual(0.1, $after - $before);
        $this->assertSame($request, $result);
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
            ->method('withParameter')
            ->with($this->identicalTo(Retry::RETRY_COUNT), $this->identicalTo(++$retryCount))
            ->will($this->returnValue($request));

        $before = microtime(true);
        $result = $this->retry->retry($request);
        $after = microtime(true);

        $this->assertGreaterThanOrEqual($delay, $after - $before);
        $this->assertLessThanOrEqual($delay + 0.1, $after - $before);
        $this->assertSame($request, $result);
    }

    /**
     * @return RetryStrategyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createStrategyMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Event\Retry\Strategy\RetryStrategyInterface');
    }

    /**
     * @return InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createRequestMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
    }
}
