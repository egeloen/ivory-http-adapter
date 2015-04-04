<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\Subscriber;

use Ivory\HttpAdapter\Event\Timer\Timer;
use Ivory\HttpAdapter\Event\Timer\TimerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Abstract timer subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractTimerSubscriber implements EventSubscriberInterface
{
    /** @var \Ivory\HttpAdapter\Event\Timer\TimerInterface */
    private $timer;

    /**
     * Creates a timer subscriber.
     *
     * @param \Ivory\HttpAdapter\Event\Timer\TimerInterface|null $timer The timer.
     */
    public function __construct(TimerInterface $timer = null)
    {
        $this->timer = $timer ?: new Timer();
    }

    /**
     * Gets the timer.
     *
     * @return \Ivory\HttpAdapter\Event\Timer\TimerInterface The timer.
     */
    public function getTimer()
    {
        return $this->timer;
    }
}
