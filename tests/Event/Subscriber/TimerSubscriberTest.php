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

/**
 * Timer subscriber test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class TimerSubscriberTest extends AbstractSubscriberTest
{
    /** @var \Ivory\HttpAdapter\Event\Subscriber\AbstractTimerSubscriber|\PHPUnit_Framework_MockObject_MockObject */
    private $timerSubscriber;

    /** @var \Ivory\HttpAdapter\Event\Timer\TimerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $timer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->timerSubscriber = $this->createTimerSubscriberMockBuilder()
            ->setConstructorArgs(array($this->timer = $this->createTimerMock()))
            ->getMockForAbstractClass();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->timer);
        unset($this->timerSubscriber);
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

    public function testSetTimer()
    {
        $this->timerSubscriber->setTimer($timer = $this->createTimerMock());

        $this->assertSame($timer, $this->timerSubscriber->getTimer());
    }

    /**
     * Creates a timer subscriber mock builder.
     *
     * @return \Ivory\HttpAdapter\Event\Subscriber\AbstractTimerSubscriber|\PHPUnit_Framework_MockObject_MockObject The timer subscriber mock builder.
     */
    private function createTimerSubscriberMockBuilder()
    {
        return $this->getMockBuilder('Ivory\HttpAdapter\Event\Subscriber\AbstractTimerSubscriber');
    }

    /**
     * Creates a timer mock.
     *
     * @return \Ivory\HttpAdapter\Event\Timer\TimerInterface|\PHPUnit_Framework_MockObject_MockObject The timer mock.
     */
    private function createTimerMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Event\Timer\TimerInterface');
    }
}
