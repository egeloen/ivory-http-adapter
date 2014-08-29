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

use Ivory\HttpAdapter\Event\Retry\LinearDelayedRetryStrategy;
use Ivory\HttpAdapter\Event\Subscriber\RetrySubscriber;

/**
 * Linear delayed retry strategy test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class LinearDelayedRetryStrategyTest extends AbstractRetryStrategyTest
{
    /** @var \Ivory\HttpAdapter\Event\Retry\LinearDelayedRetryStrategy */
    protected $linearDelayedRetryStrategy;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->linearDelayedRetryStrategy = new LinearDelayedRetryStrategy();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->linearDelayedRetryStrategy);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Retry\AbstractDelayedRetryStrategy',
            $this->linearDelayedRetryStrategy
        );

        $this->assertSame(5, $this->linearDelayedRetryStrategy->getDelay());

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
        $request = $this->createRequestMock();
        $exception = $this->createExceptionMock();

        $this->assertTrue($this->linearDelayedRetryStrategy->verify($request, $exception));
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

        $this->assertSame(0, $this->linearDelayedRetryStrategy->delay($request, $exception));
    }

    public function testDelayWithRetryCount()
    {
        $request = $this->createRequestMock();
        $exception = $this->createExceptionMock();

        $this->linearDelayedRetryStrategy->setDelay($delay = 10);

        $request
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo(RetrySubscriber::RETRY_COUNT))
            ->will($this->returnValue($retryCount = 5));

        $this->assertSame($delay * $retryCount, $this->linearDelayedRetryStrategy->delay($request, $exception));
    }
}
