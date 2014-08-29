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

use Ivory\HttpAdapter\Event\Retry\LimitedRetryStrategy;
use Ivory\HttpAdapter\Event\Subscriber\RetrySubscriber;

/**
 * Limited retry strategy test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class LimitedRetryStrategyTest extends AbstractRetryStrategyTest
{
    /** @var \Ivory\HttpAdapter\Event\Retry\LimitedRetryStrategy */
    protected $limitedRetryStrategy;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->limitedRetryStrategy = new LimitedRetryStrategy();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->limitedRetryStrategy);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Retry\AbstractRetryStrategyChain',
            $this->limitedRetryStrategy
        );

        $this->assertSame(3, $this->limitedRetryStrategy->getLimit());

        $this->assertFalse($this->limitedRetryStrategy->hasNext());
        $this->assertNull($this->limitedRetryStrategy->getNext());
    }

    public function testInitialState()
    {
        $this->limitedRetryStrategy = new LimitedRetryStrategy(
            $limit = 10,
            $next = $this->createRetryStrategyChainMock()
        );

        $this->assertSame($limit, $this->limitedRetryStrategy->getLimit());

        $this->assertTrue($this->limitedRetryStrategy->hasNext());
        $this->assertSame($next, $this->limitedRetryStrategy->getNext());
    }

    public function testSetLimit()
    {
        $this->limitedRetryStrategy->setLimit($limit = 10);

        $this->assertSame($limit, $this->limitedRetryStrategy->getLimit());
    }

    public function testVerifyWithoutRetryCount()
    {
        $request = $this->createRequestMock();
        $exception = $this->createExceptionMock();

        $request
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo(RetrySubscriber::RETRY_COUNT))
            ->will($this->returnValue(null));

        $this->assertFalse($this->limitedRetryStrategy->verify($request, $exception));
    }

    public function testVerifyWithRetryCount()
    {
        $request = $this->createRequestMock();
        $exception = $this->createExceptionMock();

        $this->limitedRetryStrategy->setLimit($limit = 5);

        $request
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo(RetrySubscriber::RETRY_COUNT))
            ->will($this->returnValue(++$limit));

        $this->assertTrue($this->limitedRetryStrategy->verify($request, $exception));
    }

    public function testDelay()
    {
        $request = $this->createRequestMock();
        $exception = $this->createExceptionMock();

        $this->assertSame(0, $this->limitedRetryStrategy->delay($request, $exception));
    }
}
