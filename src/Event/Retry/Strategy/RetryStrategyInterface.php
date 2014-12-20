<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\Retry\Strategy;

use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * Retry strategy.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface RetryStrategyInterface
{
    /**
     * Verifies if it should retry to send the request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request The request.
     *
     * @return boolean TRUE if it should retry to send the request else FALSE.
     */
    public function verify(InternalRequestInterface $request);

    /**
     * Gets the delay before retrying to send the request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request The request.
     *
     * @return float The delay before retrying to send the request.
     */
    public function delay(InternalRequestInterface $request);
}
