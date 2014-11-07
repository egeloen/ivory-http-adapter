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

use Ivory\HttpAdapter\Event\ExceptionEvent;
use Ivory\HttpAdapter\Event\PostSendEvent;
use Ivory\HttpAdapter\Event\PreSendEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Abstract timer subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractTimerSubscriber implements EventSubscriberInterface
{
    /** @var float */
    private $start;

    /**
     * On pre send event.
     *
     * @param \Ivory\HttpAdapter\Event\PreSendEvent $event The pre send event.
     */
    public function onPreSend(PreSendEvent $event)
    {
        $this->start();
    }

    /**
     * On post send event.
     *
     * @param \Ivory\HttpAdapter\Event\PostSendEvent $event The post send event.
     *
     * @return float The time.
     */
    public function onPostSend(PostSendEvent $event)
    {
        return $this->stop();
    }

    /**
     * On exception event.
     *
     * @param \Ivory\HttpAdapter\Event\ExceptionEvent $event The exception event.
     *
     * @return float The time.
     */
    public function onException(ExceptionEvent $event)
    {
        return $this->stop();
    }

    /**
     * Starts the timer.
     */
    private function start()
    {
        $this->start = microtime(true);
    }

    /**
     * Stops the timer.
     *
     * @return float The time.
     */
    private function stop()
    {
        return microtime(true) - $this->start;
    }
}
