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
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Abstract timer subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractTimerSubscriber implements EventSubscriberInterface
{
    /** @const string The timer parameter */
    const TIMER = 'timer';

    /**
     * On pre send event.
     *
     * @param \Ivory\HttpAdapter\Event\PreSendEvent $event The pre send event.
     */
    public function onPreSend(PreSendEvent $event)
    {
        $this->start($event->getRequest());
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
        return $this->stop($event->getRequest());
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
        return $this->stop($event->getRequest());
    }

    /**
     * Starts the timer.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     */
    private function start(InternalRequestInterface $internalRequest)
    {
        $internalRequest->setParameter(self::TIMER, microtime(true));
    }

    /**
     * Stops the timer.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     *
     * @return float The time.
     */
    private function stop(InternalRequestInterface $internalRequest)
    {
        $time = microtime(true) - $internalRequest->getParameter(self::TIMER);
        $internalRequest->removeParameter(self::TIMER);

        return $time;
    }
}
