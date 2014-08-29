<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\Subscriber;

use Ivory\HttpAdapter\Event\Events;
use Ivory\HttpAdapter\Event\Subscriber\RetrySubscriber;

/**
 * Retry subscriber test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class RetrySubscriberTest extends AbstractSubscriberTest
{
    /** @var \Ivory\HttpAdapter\Event\Subscriber\RetrySubscriber */
    protected $retrySubscriber;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->retrySubscriber = new RetrySubscriber();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->retryStrategy);
        unset($this->retrySubscriber);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Retry\LimitedRetryStrategy',
            $limitedStrategy = $this->retrySubscriber->getStrategy()
        );

        $this->assertTrue($limitedStrategy->hasNext());
        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Retry\ExponentialDelayedRetryStrategy',
            $exponentialDelayedStrategy = $limitedStrategy->getNext()
        );

        $this->assertFalse($exponentialDelayedStrategy->hasNext());
    }

    public function testInitialState()
    {
        $this->retrySubscriber = new RetrySubscriber($strategy = $this->createRetryStrategyMock());

        $this->assertSame($strategy, $this->retrySubscriber->getStrategy());
    }

    public function testSetStrategy()
    {
        $this->retrySubscriber->setStrategy($strategy = $this->createRetryStrategyMock());

        $this->assertSame($strategy, $this->retrySubscriber->getStrategy());
    }

    public function testSubscribedEvents()
    {
        $events = RetrySubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::EXCEPTION, $events);
        $this->assertSame(array('onException', 0), $events[Events::EXCEPTION]);
    }

    public function testExceptionEventWithStrategyNotVerified()
    {
        $request = $this->createRequestMock();
        $exception = $this->createExceptionMock();

        $this->retrySubscriber->setStrategy($strategy = $this->createRetryStrategyMock());

        $strategy
            ->expects($this->once())
            ->method('verify')
            ->with($this->identicalTo($request), $this->identicalTo($exception))
            ->will($this->returnValue(false));

        $request
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo(RetrySubscriber::RETRY_COUNT))
            ->will($this->returnValue($retryCount = null));

        $request
            ->expects($this->once())
            ->method('setParameter')
            ->with($this->identicalTo(RetrySubscriber::RETRY_COUNT), $this->identicalTo((int) $retryCount));

        $this->retrySubscriber->onException($this->createExceptionEvent(null, $request, $exception));
    }

    public function testExceptionEventWithStrategyVerified()
    {
        $httpAdapter = $this->createHttpAdapterMock();
        $request = $this->createRequestMock();
        $exception = $this->createExceptionMock();

        $this->retrySubscriber->setStrategy($strategy = $this->createRetryStrategyMock());

        $strategy
            ->expects($this->once())
            ->method('verify')
            ->with($this->identicalTo($request), $this->identicalTo($exception))
            ->will($this->returnValue(true));

        $request
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo(RetrySubscriber::RETRY_COUNT))
            ->will($this->returnValue($retryCount = null));

        $request
            ->expects($this->once())
            ->method('setParameter')
            ->with($this->identicalTo(RetrySubscriber::RETRY_COUNT), $this->identicalTo(++$retryCount));

        $httpAdapter
            ->expects($this->once())
            ->method('sendInternalRequest')
            ->with($this->identicalTo($request))
            ->will($this->returnValue($response = $this->createResponseMock()));

        $exceptionEvent = $this->createExceptionEvent($httpAdapter, $request, $exception);

        $before = microtime(true);
        $this->retrySubscriber->onException($exceptionEvent);
        $after = microtime(true);

        $this->assertLessThanOrEqual(0.1, $after - $before);

        $this->assertTrue($exceptionEvent->hasResponse());
        $this->assertSame($response, $exceptionEvent->getResponse());
    }

    public function testExceptionEventWithStrategyDelayed()
    {
        $httpAdapter = $this->createHttpAdapterMock();
        $request = $this->createRequestMock();
        $exception = $this->createExceptionMock();

        $this->retrySubscriber->setStrategy($strategy = $this->createRetryStrategyMock());

        $strategy
            ->expects($this->once())
            ->method('verify')
            ->with($this->identicalTo($request), $this->identicalTo($exception))
            ->will($this->returnValue(true));

        $strategy
            ->expects($this->once())
            ->method('delay')
            ->with($this->identicalTo($request), $this->identicalTo($exception))
            ->will($this->returnValue($delay = 0.5));

        $request
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo(RetrySubscriber::RETRY_COUNT))
            ->will($this->returnValue($retryCount = 1));

        $request
            ->expects($this->once())
            ->method('setParameter')
            ->with($this->identicalTo(RetrySubscriber::RETRY_COUNT), $this->identicalTo(++$retryCount));

        $httpAdapter
            ->expects($this->once())
            ->method('sendInternalRequest')
            ->with($this->identicalTo($request))
            ->will($this->returnValue($response = $this->createResponseMock()));

        $exceptionEvent = $this->createExceptionEvent($httpAdapter, $request, $exception);

        $before = microtime(true);
        $this->retrySubscriber->onException($exceptionEvent);
        $after = microtime(true);

        $this->assertGreaterThanOrEqual($delay, $after - $before);
        $this->assertLessThanOrEqual($delay + 0.1, $after - $before);

        $this->assertTrue($exceptionEvent->hasResponse());
        $this->assertSame($response, $exceptionEvent->getResponse());
    }

    /**
     * Creates a retry strategy mock.
     *
     * @return \Ivory\HttpAdapter\Event\Retry\RetryStrategyInterface|\PHPUnit_Framework_MockObject_MockObject The retry strategy mock.
     */
    protected function createRetryStrategyMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Event\Retry\RetryStrategyInterface');
    }
}
