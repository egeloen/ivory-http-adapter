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

use Ivory\HttpAdapter\Event\Retry\Strategy\AbstractRetryStrategyChain;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RetryStrategyChainTest extends AbstractRetryStrategyTest
{
    /**
     * @var AbstractRetryStrategyChain|\PHPUnit_Framework_MockObject_MockObject
     */
    private $retryStrategyChain;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->retryStrategyChain = $this->createRetryStrategyChainMockBuilder()->getMockForAbstractClass();
    }

    public function testDefaultState()
    {
        $this->assertFalse($this->retryStrategyChain->hasNext());
        $this->assertNull($this->retryStrategyChain->getNext());
    }

    public function testInitialState()
    {
        $this->retryStrategyChain = $this->createRetryStrategyChainMockBuilder()
            ->setConstructorArgs([$next = $this->createRetryStrategyChainMock()])
            ->getMockForAbstractClass();

        $this->assertTrue($this->retryStrategyChain->hasNext());
        $this->assertSame($next, $this->retryStrategyChain->getNext());
    }

    public function testSetNext()
    {
        $this->retryStrategyChain->setNext($next = $this->createRetryStrategyChainMock());

        $this->assertTrue($this->retryStrategyChain->hasNext());
        $this->assertSame($next, $this->retryStrategyChain->getNext());
    }

    public function testVerifyWithoutChain()
    {
        $this->assertTrue($this->retryStrategyChain->verify($this->createRequestMock()));
    }

    public function testVerifyWithChainVerified()
    {
        $this->retryStrategyChain->setNext($next = $this->createRetryStrategyChainMock());

        $next
            ->expects($this->once())
            ->method('verify')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue(true));

        $this->assertTrue($this->retryStrategyChain->verify($request));
    }

    public function testVerifyWithChainNotVerified()
    {
        $this->retryStrategyChain->setNext($next = $this->createRetryStrategyChainMock());

        $next
            ->expects($this->once())
            ->method('verify')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue(false));

        $this->assertFalse($this->retryStrategyChain->verify($request));
    }

    public function testDelayWithoutChain()
    {
        $delay = $this->retryStrategyChain->delay($this->createRequestMock());

        $this->assertSame(0, $delay);
    }

    public function testDelayWithChainDelayed()
    {
        $this->retryStrategyChain->setNext($next = $this->createRetryStrategyChainMock());

        $next
            ->expects($this->once())
            ->method('delay')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue($delay = 1));

        $this->assertSame($delay, $this->retryStrategyChain->delay($request));
    }

    public function testDelayWithChainNotDelayed()
    {
        $this->retryStrategyChain->setNext($next = $this->createRetryStrategyChainMock());

        $next
            ->expects($this->once())
            ->method('delay')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue($delay = 0));

        $this->assertSame($delay, $this->retryStrategyChain->delay($request));
    }

    /**
     * @return AbstractRetryStrategyChain|\PHPUnit_Framework_MockObject_MockBuilder
     */
    private function createRetryStrategyChainMockBuilder()
    {
        return $this->getMockBuilder('Ivory\HttpAdapter\Event\Retry\Strategy\AbstractRetryStrategyChain');
    }
}
