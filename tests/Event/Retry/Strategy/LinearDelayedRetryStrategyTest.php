<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\Retry\Strategy;

use Ivory\HttpAdapter\Event\Retry\RetryInterface;
use Ivory\HttpAdapter\Event\Retry\Strategy\LinearDelayedRetryStrategy;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LinearDelayedRetryStrategyTest extends AbstractRetryStrategyTest
{
    /**
     * @var LinearDelayedRetryStrategy
     */
    private $linearDelayedRetryStrategy;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->linearDelayedRetryStrategy = new LinearDelayedRetryStrategy();
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Retry\Strategy\AbstractDelayedRetryStrategy',
            $this->linearDelayedRetryStrategy
        );

        $this->assertSame(5.0, $this->linearDelayedRetryStrategy->getDelay());

        $this->assertFalse($this->linearDelayedRetryStrategy->hasNext());
        $this->assertNull($this->linearDelayedRetryStrategy->getNext());
    }

    public function testInitialState()
    {
        $this->linearDelayedRetryStrategy = new LinearDelayedRetryStrategy(
            $delay = 10,
            $next = $this->createRetryStrategyChainMock()
        );

        $this->assertSame($delay, $this->linearDelayedRetryStrategy->getDelay());

        $this->assertTrue($this->linearDelayedRetryStrategy->hasNext());
        $this->assertSame($next, $this->linearDelayedRetryStrategy->getNext());
    }

    public function testVerify()
    {
        $this->assertTrue($this->linearDelayedRetryStrategy->verify($this->createRequestMock()));
    }

    public function testDelayWithoutRetryCount()
    {
        $request = $this->createRequestMock();
        $request
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo(RetryInterface::RETRY_COUNT));

        $this->assertSame(0.0, $this->linearDelayedRetryStrategy->delay($request));
    }

    public function testDelayWithRetryCount()
    {
        $this->linearDelayedRetryStrategy->setDelay($delay = 10);

        $request = $this->createRequestMock();
        $request
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo(RetryInterface::RETRY_COUNT))
            ->will($this->returnValue($retryCount = 5));

        $this->assertSame($delay * $retryCount, $this->linearDelayedRetryStrategy->delay($request));
    }
}
