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
    protected $start;

    /** @var float */
    protected $time;

    /**
     * On pre send event.
     *
     * @param \Ivory\HttpAdapter\Event\PreSendEvent $event The pre send event.
     */
    public function onPreSend(PreSendEvent $event)
    {
        $this->start = microtime(true);
    }

    /**
     * On post send event.
     *
     * @param \Ivory\HttpAdapter\Event\PostSendEvent $event The post send event.
     */
    public function onPostSend(PostSendEvent $event)
    {
        $this->time = microtime(true) - $this->start;
    }
}
