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

use Ivory\HttpAdapter\Event\Retry\ConstantDelayedRetryStrategy;

/**
 * Constant delayed retry strategy test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ConstantDelayedRetryStrategyTest extends AbstractRetryStrategyTest
{
    /** @var \Ivory\HttpAdapter\Event\Retry\ConstantDelayedRetryStrategy */
    protected $constantDelayedRetryStrategy;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->constantDelayedRetryStrategy = new ConstantDelayedRetryStrategy();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->constantDelayedRetryStrategy);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Retry\AbstractDelayedRetryStrategy',
            $this->constantDelayedRetryStrategy
        );

        $this->assertSame(5, $this->constantDelayedRetryStrategy->getDelay());

        $this->assertFalse($this->constantDelayedRetryStrategy->hasNext());
        $this->assertNull($this->constantDelayedRetryStrategy->getNext());
    }

    public function testInitialState()
    {
        $this->constantDelayedRetryStrategy = new ConstantDelayedRetryStrategy(
            $delay = 10,
            $next = $this->createRetryStrategyChainMock()
        );

        $this->assertSame($delay, $this->constantDelayedRetryStrategy->getDelay());

        $this->assertTrue($this->constantDelayedRetryStrategy->hasNext());
        $this->assertSame($next, $this->constantDelayedRetryStrategy->getNext());
    }

    public function testVerify()
    {
        $request = $this->createRequestMock();
        $exception = $this->createExceptionMock();

        $this->assertTrue($this->constantDelayedRetryStrategy->verify($request, $exception));
    }

    public function testDelay()
    {
        $request = $this->createRequestMock();
        $exception = $this->createExceptionMock();

        $this->constantDelayedRetryStrategy->setDelay($delay = 10);

        $this->assertSame($delay, $this->constantDelayedRetryStrategy->delay($request, $exception));
    }
}
