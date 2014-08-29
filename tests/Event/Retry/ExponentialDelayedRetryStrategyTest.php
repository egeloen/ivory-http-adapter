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

use Ivory\HttpAdapter\Event\Retry\ExponentialDelayedRetryStrategy;
use Ivory\HttpAdapter\Event\Subscriber\RetrySubscriber;

/**
 * Exponential delayed retry strategy test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ExponentialDelayedRetryStrategyTest extends AbstractRetryStrategyTest
{
    /** @var \Ivory\HttpAdapter\Event\Retry\ExponentialDelayedRetryStrategy */
    protected $exponentialRetryStrategy;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->exponentialRetryStrategy = new ExponentialDelayedRetryStrategy();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->exponentialRetryStrategy);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Retry\AbstractRetryStrategyChain',
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
        $request = $this->createRequestMock();
        $exception = $this->createExceptionMock();

        $this->assertTrue($this->exponentialRetryStrategy->verify($request, $exception));
    }

    public function testDelayWithoutRetryCount()
    {
        $request = $this->createRequestMock();
        $exception = $this->createExceptionMock();

        $request
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo(RetrySubscriber::RETRY_COUNT))
            ->will($this->returnValue(null));

        $this->assertSame(1, $this->exponentialRetryStrategy->delay($request, $exception));
    }

    public function testDelayWithRetryCount()
    {
        $request = $this->createRequestMock();
        $exception = $this->createExceptionMock();

        $request
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo(RetrySubscriber::RETRY_COUNT))
            ->will($this->returnValue(5));

        $this->assertSame(32, $this->exponentialRetryStrategy->delay($request, $exception));
    }
}
