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
use Ivory\HttpAdapter\Event\Subscriber\StopwatchSubscriber;

/**
 * Stopwatch subscriber test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StopwatchSubscriberTest extends AbstractSubscriberTest
{
    /** @var \Ivory\HttpAdapter\Event\Subscriber\StopwatchSubscriber */
    private $stopwatchSubscriber;

    /** @var \Symfony\Component\Stopwatch\Stopwatch|\PHPUnit_Framework_MockObject_MockObject */
    private $stopwatch;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->stopwatchSubscriber = new StopwatchSubscriber(
            $this->stopwatch = $this->getMock('Symfony\Component\Stopwatch\Stopwatch')
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->stopwatch);
        unset($this->stopwatchSubscriber);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->stopwatch, $this->stopwatchSubscriber->getStopwatch());
    }

    public function testSetStopwatch()
    {
        $this->stopwatchSubscriber->setStopwatch($stopwatch = $this->getMock('Symfony\Component\Stopwatch\Stopwatch'));

        $this->assertSame($stopwatch, $this->stopwatchSubscriber->getStopwatch());
    }

    public function testSubscribedEvents()
    {
        $events = StopwatchSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::PRE_SEND, $events);
        $this->assertSame(array('onPreSend', 10000), $events[Events::PRE_SEND]);

        $this->assertArrayHasKey(Events::POST_SEND, $events);
        $this->assertSame(array('onPostSend', 10000), $events[Events::POST_SEND]);

        $this->assertArrayHasKey(Events::EXCEPTION, $events);
        $this->assertSame(array('onException', 10000), $events[Events::EXCEPTION]);
    }

    public function testPreSendEvent()
    {
        $this->stopwatch
            ->expects($this->once())
            ->method('start')
            ->with($this->identicalTo('ivory.http_adapter.http_adapter (url)'));

        $this->stopwatchSubscriber->onPreSend($this->createPreSendEvent());
    }

    public function testPostSendEvent()
    {
        $this->stopwatch
            ->expects($this->once())
            ->method('stop')
            ->with($this->identicalTo('ivory.http_adapter.http_adapter (url)'));

        $this->stopwatchSubscriber->onPostSend($this->createPostSendEvent());
    }

    public function testExceptionEvent()
    {
        $this->stopwatch
            ->expects($this->once())
            ->method('stop')
            ->with($this->identicalTo('ivory.http_adapter.http_adapter (url)'));

        $this->stopwatchSubscriber->onException($this->createExceptionEvent());
    }

    /**
     * {@inheritdoc}
     */
    protected function createHttpAdapterMock()
    {
        $httpAdapter = parent::createHttpAdapterMock();
        $httpAdapter
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('name'));

        return $httpAdapter;
    }

    /**
     * {@inheritdoc}
     */
    protected function createRequestMock()
    {
        $request = parent::createRequestMock();
        $request
            ->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue('url'));

        return $request;
    }
}
