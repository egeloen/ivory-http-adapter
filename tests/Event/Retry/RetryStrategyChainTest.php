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
 * Retry strategy chain test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class RetryStrategyChainTest extends AbstractRetryStrategyTest
{
    /** @var \Ivory\HttpAdapter\Event\Retry\AbstractRetryStrategyChain|\PHPUnit_Framework_MockObject_MockObject */
    protected $retryStrategyChain;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->retryStrategyChain = $this->createRetryStrategyChainMockBuilder()->getMockForAbstractClass();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->retryStrategyChain);
    }

    public function testDefaultState()
    {
        $this->assertFalse($this->retryStrategyChain->hasNext());
        $this->assertNull($this->retryStrategyChain->getNext());
    }

    public function testInitialState()
    {
        $this->retryStrategyChain = $this->createRetryStrategyChainMockBuilder()
            ->setConstructorArgs(array($next = $this->createRetryStrategyChainMock()))
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
        $this->assertTrue($this->retryStrategyChain->verify($this->createRequestMock(), $this->createExceptionMock()));
    }

    public function testVerifyWithChainVerified()
    {
        $request = $this->createRequestMock();
        $exception = $this->createExceptionMock();

        $this->retryStrategyChain->setNext($next = $this->createRetryStrategyChainMock());

        $next
            ->expects($this->once())
            ->method('verify')
            ->with($this->identicalTo($request), $this->identicalTo($exception))
            ->will($this->returnValue(true));

        $this->assertTrue($this->retryStrategyChain->verify($request, $exception));
    }

    public function testVerifyWithChainNotVerified()
    {
        $request = $this->createRequestMock();
        $exception = $this->createExceptionMock();

        $this->retryStrategyChain->setNext($next = $this->createRetryStrategyChainMock());

        $next
            ->expects($this->once())
            ->method('verify')
            ->with($this->identicalTo($request), $this->identicalTo($exception))
            ->will($this->returnValue(false));

        $this->assertFalse($this->retryStrategyChain->verify($request, $exception));
    }

    public function testDelayWithoutChain()
    {
        $delay = $this->retryStrategyChain->delay($this->createRequestMock(), $this->createExceptionMock());

        $this->assertSame(0, $delay);
    }

    public function testDelayWithChainDelayed()
    {
        $request = $this->createRequestMock();
        $exception = $this->createExceptionMock();

        $this->retryStrategyChain->setNext($next = $this->createRetryStrategyChainMock());

        $next
            ->expects($this->once())
            ->method('delay')
            ->with($this->identicalTo($request), $this->identicalTo($exception))
            ->will($this->returnValue($delay = 1));

        $this->assertSame($delay, $this->retryStrategyChain->delay($request, $exception));
    }

    public function testDelayWithChainNotDelayed()
    {
        $request = $this->createRequestMock();
        $exception = $this->createExceptionMock();

        $this->retryStrategyChain->setNext($next = $this->createRetryStrategyChainMock());

        $next
            ->expects($this->once())
            ->method('delay')
            ->with($this->identicalTo($request), $this->identicalTo($exception))
            ->will($this->returnValue($delay = 0));

        $this->assertSame($delay, $this->retryStrategyChain->delay($request, $exception));
    }

    /**
     * Creates a retry strategy chain mock builder.
     *
     * @return \PHPUnit_Framework_MockObject_MockBuilder The retry strategy chain mock builder.
     */
    protected function createRetryStrategyChainMockBuilder()
    {
        return $this->getMockBuilder('Ivory\HttpAdapter\Event\Retry\AbstractRetryStrategyChain');
    }
}
