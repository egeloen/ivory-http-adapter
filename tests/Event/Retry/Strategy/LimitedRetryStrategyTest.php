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
use Ivory\HttpAdapter\Event\Retry\Strategy\LimitedRetryStrategy;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LimitedRetryStrategyTest extends AbstractRetryStrategyTest
{
    /**
     * @var LimitedRetryStrategy
     */
    private $limitedRetryStrategy;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->limitedRetryStrategy = new LimitedRetryStrategy();
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Retry\Strategy\AbstractRetryStrategyChain',
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
        $request
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo(RetryInterface::RETRY_COUNT));

        $this->assertTrue($this->limitedRetryStrategy->verify($request));
    }

    public function testVerifyWithRetryCount()
    {
        $this->limitedRetryStrategy->setLimit($limit = 5);

        $request = $this->createRequestMock();
        $request
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo(RetryInterface::RETRY_COUNT))
            ->will($this->returnValue(++$limit));

        $this->assertFalse($this->limitedRetryStrategy->verify($request));
    }

    public function testDelay()
    {
        $this->assertSame(0, $this->limitedRetryStrategy->delay($this->createRequestMock()));
    }
}
