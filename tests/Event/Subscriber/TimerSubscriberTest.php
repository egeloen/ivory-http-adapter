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
use Ivory\HttpAdapter\Event\Timer\TimerInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TimerSubscriberTest extends AbstractSubscriberTest
{
    /**
     * @var AbstractTimerSubscriber|\PHPUnit_Framework_MockObject_MockObject
     */
    private $timerSubscriber;

    /**
     * @var TimerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $timer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->timerSubscriber = $this->createTimerSubscriberMockBuilder()
            ->setConstructorArgs([$this->timer = $this->createTimerMock()])
            ->getMockForAbstractClass();
    }

    public function testDefaultState()
    {
        $this->timerSubscriber = $this->createTimerSubscriberMockBuilder()->getMockForAbstractClass();

        $this->assertInstanceOf('Ivory\HttpAdapter\Event\Timer\Timer', $this->timerSubscriber->getTimer());
    }

    public function testInitialState()
    {
        $this->assertSame($this->timer, $this->timerSubscriber->getTimer());
    }

    /**
     * @return AbstractTimerSubscriber|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createTimerSubscriberMockBuilder()
    {
        return $this->getMockBuilder('Ivory\HttpAdapter\Event\Subscriber\AbstractTimerSubscriber');
    }

    /**
     * @return TimerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createTimerMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Event\Timer\TimerInterface');
    }
}
