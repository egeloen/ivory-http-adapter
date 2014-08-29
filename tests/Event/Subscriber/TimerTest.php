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
 * Timer test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class TimerTest extends AbstractSubscriberTest
{
    /** @var \Ivory\HttpAdapter\Event\Subscriber\AbstractTimerSubscriber|\PHPUnit_Framework_MockObject_MockObject */
    protected $timerSubscriber;

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
        $this->timerSubscriber->onPreSend($this->createPreSendEvent());
        $this->timerSubscriber->onPostSend($this->createPostSendEvent());

        $reflectionProperty = new \ReflectionProperty($this->timerSubscriber, 'time');
        $reflectionProperty->setAccessible(true);

        $time = $reflectionProperty->getValue($this->timerSubscriber);

        $this->assertGreaterThanOrEqual(0, $time);
        $this->assertLessThanOrEqual(1, $time);
    }
}
