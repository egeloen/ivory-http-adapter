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

/**
 * Delayed retry strategy test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class DelayedRetryStrategyTest extends AbstractRetryStrategyTest
{
    /** @var \Ivory\HttpAdapter\Event\Retry\AbstractDelayedRetryStrategy */
    protected $delayedRetryStrategy;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->delayedRetryStrategy = $this->createDelayedRetryStrategyMockBuilder()->getMockForAbstractClass();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->delayedRetryStrategy);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Retry\AbstractRetryStrategyChain',
            $this->delayedRetryStrategy
        );

        $this->assertSame(5, $this->delayedRetryStrategy->getDelay());

        $this->assertFalse($this->delayedRetryStrategy->hasNext());
        $this->assertNull($this->delayedRetryStrategy->getNext());
    }

    public function testInitialState()
    {
        $this->delayedRetryStrategy = $this->createDelayedRetryStrategyMockBuilder()
            ->setConstructorArgs(array($delay = 10, $next = $this->createRetryStrategyChainMock()))
            ->getMockForAbstractClass();

        $this->assertSame($delay, $this->delayedRetryStrategy->getDelay());

        $this->assertTrue($this->delayedRetryStrategy->hasNext());
        $this->assertSame($next, $this->delayedRetryStrategy->getNext());
    }

    public function testSetDelay()
    {
        $this->delayedRetryStrategy->setDelay($delay = 10);

        $this->assertSame($delay, $this->delayedRetryStrategy->getDelay());
    }

    /**
     * Creates a delayed retry strategy mock builder.
     *
     * @return \PHPUnit_Framework_MockObject_MockBuilder The delayed retry strategy mock builder.
     */
    protected function createDelayedRetryStrategyMockBuilder()
    {
        return $this->getMockBuilder('Ivory\HttpAdapter\Event\Retry\AbstractDelayedRetryStrategy');
    }
}
