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
    private $retrySubscriber;

    /** @var \Ivory\HttpAdapter\Event\Retry\RetryInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $retry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->retrySubscriber = new RetrySubscriber($this->retry = $this->createRetryMock());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->retry);
        unset($this->retrySubscriber);
    }

    public function testDefaultState()
    {
        $this->retrySubscriber = new RetrySubscriber();

        $this->assertInstanceOf('Ivory\HttpAdapter\Event\Retry\Retry', $this->retrySubscriber->getRetry());
    }

    public function testInitialState()
    {
        $this->assertSame($this->retry, $this->retrySubscriber->getRetry());
    }

    public function testSetRetry()
    {
        $this->retrySubscriber->setRetry($retry = $this->createRetryMock());

        $this->assertSame($retry, $this->retrySubscriber->getRetry());
    }

    public function testSubscribedEvents()
    {
        $events = RetrySubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::EXCEPTION, $events);
        $this->assertSame(array('onException', 0), $events[Events::EXCEPTION]);
    }

    public function testExceptionEventRetried()
    {
        $this->retry
            ->expects($this->once())
            ->method('retry')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue(true));

        $httpAdapter = $this->createHttpAdapterMock();
        $httpAdapter
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->identicalTo($request))
            ->will($this->returnValue($retryResponse = $this->createResponseMock()));

        $this->retrySubscriber->onException(
            $event = $this->createExceptionEvent($httpAdapter, $this->createExceptionMock($request))
        );

        $this->assertSame($retryResponse, $event->getResponse());
    }

    public function testExceptionEventRetriedThrowException()
    {
        $this->retry
            ->expects($this->once())
            ->method('retry')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue(true));

        $httpAdapter = $this->createHttpAdapterMock();
        $httpAdapter
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->identicalTo($request))
            ->will($this->throwException($exception = $this->createExceptionMock()));

        $this->retrySubscriber->onException(
            $event = $this->createExceptionEvent($httpAdapter, $this->createExceptionMock($request))
        );

        $this->assertFalse($event->hasResponse());
        $this->assertSame($exception, $event->getException());
    }

    public function testExceptionEventNotRetried()
    {
        $this->retry
            ->expects($this->once())
            ->method('retry')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue(false));

        $httpAdapter = $this->createHttpAdapterMock();
        $httpAdapter
            ->expects($this->never())
            ->method('sendRequest');

        $this->retrySubscriber->onException($event = $this->createExceptionEvent(
            $httpAdapter,
            $this->createExceptionMock($request)
        ));

        $this->assertNull($event->getResponse());
    }

    /**
     * Creates a retry mock.
     *
     * @return \Ivory\HttpAdapter\Event\Retry\RetryInterface|\PHPUnit_Framework_MockObject_MockObject The retry mock.
     */
    private function createRetryMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Event\Retry\RetryInterface');
    }
}
