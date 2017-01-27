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

use Ivory\HttpAdapter\Event\Retry\Strategy\AbstractDelayedRetryStrategy;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DelayedRetryStrategyTest extends AbstractRetryStrategyTest
{
    /**
     * @var AbstractDelayedRetryStrategy
     */
    private $delayedRetryStrategy;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->delayedRetryStrategy = $this->createDelayedRetryStrategyMockBuilder()->getMockForAbstractClass();
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Retry\Strategy\AbstractRetryStrategyChain',
            $this->delayedRetryStrategy
        );

        $this->assertSame(5.0, $this->delayedRetryStrategy->getDelay());

        $this->assertFalse($this->delayedRetryStrategy->hasNext());
        $this->assertNull($this->delayedRetryStrategy->getNext());
    }

    public function testInitialState()
    {
        $this->delayedRetryStrategy = $this->createDelayedRetryStrategyMockBuilder()
            ->setConstructorArgs([$delay = 10, $next = $this->createRetryStrategyChainMock()])
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
     * @return AbstractDelayedRetryStrategy|\PHPUnit_Framework_MockObject_MockBuilder
     */
    private function createDelayedRetryStrategyMockBuilder()
    {
        return $this->getMockBuilder('Ivory\HttpAdapter\Event\Retry\Strategy\AbstractDelayedRetryStrategy');
    }
}
