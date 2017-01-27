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
use Ivory\HttpAdapter\Event\Retry\Strategy\ExponentialDelayedRetryStrategy;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ExponentialDelayedRetryStrategyTest extends AbstractRetryStrategyTest
{
    /**
     * @var ExponentialDelayedRetryStrategy
     */
    private $exponentialRetryStrategy;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->exponentialRetryStrategy = new ExponentialDelayedRetryStrategy();
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Retry\Strategy\AbstractRetryStrategyChain',
            $this->exponentialRetryStrategy
        );

        $this->assertFalse($this->exponentialRetryStrategy->hasNext());
        $this->assertNull($this->exponentialRetryStrategy->getNext());
    }

    public function testInitialState()
    {
        $this->exponentialRetryStrategy = new ExponentialDelayedRetryStrategy(
            $next = $this->createRetryStrategyChainMock()
        );

        $this->assertTrue($this->exponentialRetryStrategy->hasNext());
        $this->assertSame($next, $this->exponentialRetryStrategy->getNext());
    }

    public function testVerify()
    {
        $this->assertTrue($this->exponentialRetryStrategy->verify($this->createRequestMock()));
    }

    public function testDelayWithoutRetryCount()
    {
        $request = $this->createRequestMock();
        $request
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo(RetryInterface::RETRY_COUNT));

        $this->assertSame(1, $this->exponentialRetryStrategy->delay($request));
    }

    public function testDelayWithRetryCount()
    {
        $request = $this->createRequestMock();
        $request
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo(RetryInterface::RETRY_COUNT))
            ->will($this->returnValue(5));

        $this->assertSame(32, $this->exponentialRetryStrategy->delay($request));
    }
}
