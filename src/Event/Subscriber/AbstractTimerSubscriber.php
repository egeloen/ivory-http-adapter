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
     * Starts the timer.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     */
    protected function startTimer(InternalRequestInterface $internalRequest)
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
    protected function stopTimer(InternalRequestInterface $internalRequest)
    {
        $time = microtime(true) - $internalRequest->getParameter(self::TIMER);
        $internalRequest->removeParameter(self::TIMER);

        return $time;
    }
}
