<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\Timer;

use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * Timer.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface TimerInterface
{
    /** @const string The start time parameter */
    const START_TIME = 'start_time';

    /** @const string The time parameter */
    const TIME = 'time';

    /**
     * Starts the timer.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The started internal request.
     */
    public function start(InternalRequestInterface $internalRequest);

    /**
     * Stops the timer.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The stopped internal request.
     */
    public function stop(InternalRequestInterface $internalRequest);
}
