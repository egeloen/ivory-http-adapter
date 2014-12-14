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

    public function testExceptionEvent()
    {
        $this->retry
            ->expects($this->once())
            ->method('retry')
            ->with(
                $this->identicalTo($request = $this->createRequestMock()),
                $this->identicalTo($httpAdapter = $this->createHttpAdapterMock())
            )
            ->will($this->returnValue($response = $this->createResponseMock()));

        $this->retrySubscriber->onException(
            $event = $this->createExceptionEvent($httpAdapter, $this->createExceptionMock($request))
        );

        $this->assertSame($response, $event->getResponse());
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
