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

use Ivory\HttpAdapter\Event\Subscriber\AbstractTimerSubscriber;

/**
 * Timer test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class TimerTest extends AbstractSubscriberTest
{
    /** @var \Ivory\HttpAdapter\Event\Subscriber\AbstractTimerSubscriber|\PHPUnit_Framework_MockObject_MockObject */
    private $timerSubscriber;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->timerSubscriber = $this->getMockBuilder('Ivory\HttpAdapter\Event\Subscriber\AbstractTimerSubscriber')
            ->getMockForAbstractClass();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->timerSubscriber);
    }

    public function testPostSendEvent()
    {
        $request = $this->createRequestMock();
        $timer = null;

        $request
            ->expects($this->once())
            ->method('setParameter')
            ->with(
                $this->identicalTo(AbstractTimerSubscriber::TIMER),
                $this->callback(function ($parameter) use (&$timer) {
                    $timer = $parameter;

                    return true;
                })
            );

        $request
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo(AbstractTimerSubscriber::TIMER))
            ->will($this->returnCallback(function() use (&$timer) {
                return $timer;
            }));

        $request
            ->expects($this->once())
            ->method('removeParameter')
            ->with($this->identicalTo(AbstractTimerSubscriber::TIMER));

        $this->timerSubscriber->onPreSend($this->createPreSendEvent(null, $request));
        $time = $this->timerSubscriber->onPostSend($this->createPostSendEvent(null, $request));

        $this->assertGreaterThanOrEqual(0, $time);
        $this->assertLessThanOrEqual(1, $time);
    }

    public function testExceptionEvent()
    {
        $request = $this->createRequestMock();
        $timer = null;

        $request
            ->expects($this->once())
            ->method('setParameter')
            ->with(
                $this->identicalTo(AbstractTimerSubscriber::TIMER),
                $this->callback(function ($parameter) use (&$timer) {
                    $timer = $parameter;

                    return true;
                })
            );

        $request
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo(AbstractTimerSubscriber::TIMER))
            ->will($this->returnCallback(function() use (&$timer) {
                return $timer;
            }));

        $request
            ->expects($this->once())
            ->method('removeParameter')
            ->with($this->identicalTo(AbstractTimerSubscriber::TIMER));

        $this->timerSubscriber->onPreSend($this->createPreSendEvent(null, $request));
        $time = $this->timerSubscriber->onException($this->createExceptionEvent(null, $request));

        $this->assertGreaterThanOrEqual(0, $time);
        $this->assertLessThanOrEqual(1, $time);
    }
}
