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
 * @author GeLo <geloen.eric@gmail.com>
 */
interface TimerInterface
{
    const START_TIME = 'start_time';
    const TIME = 'time';

    /**
     * @param InternalRequestInterface $internalRequest
     *
     * @return InternalRequestInterface
     */
    public function start(InternalRequestInterface $internalRequest);

    /**
     * @param InternalRequestInterface $internalRequest
     *
     * @return InternalRequestInterface
     */
    public function stop(InternalRequestInterface $internalRequest);
}
