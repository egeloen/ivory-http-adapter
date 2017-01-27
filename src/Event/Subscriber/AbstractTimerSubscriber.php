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
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractTimerSubscriber implements EventSubscriberInterface
{
    /**
     * @var TimerInterface
     */
    private $timer;

    /**
     * @param TimerInterface|null $timer
     */
    public function __construct(TimerInterface $timer = null)
    {
        $this->timer = $timer ?: new Timer();
    }

    /**
     * @return TimerInterface
     */
    public function getTimer()
    {
        return $this->timer;
    }
}
