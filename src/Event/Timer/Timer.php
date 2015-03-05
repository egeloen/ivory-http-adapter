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
class Timer implements TimerInterface
{
    /**
     * {@inheritdoc}
     */
    public function start(InternalRequestInterface $internalRequest)
    {
        return $internalRequest
            ->withParameter(self::START_TIME, $this->getTime())
            ->withoutParameter(self::TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function stop(InternalRequestInterface $internalRequest)
    {
        if ($internalRequest->hasParameter(self::START_TIME) && !$internalRequest->hasParameter(self::TIME)) {
            return $internalRequest->withParameter(
                self::TIME,
                $this->getTime() - $internalRequest->getParameter(self::START_TIME)
            );
        }

        return $internalRequest;
    }

    /**
     * Gets the time.
     *
     * @return float The time.
     */
    private function getTime()
    {
        return microtime(true);
    }
}
