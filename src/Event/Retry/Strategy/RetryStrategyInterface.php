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
 * @author GeLo <geloen.eric@gmail.com>
 */
interface RetryStrategyInterface
{
    /**
     * @param InternalRequestInterface $request
     *
     * @return bool
     */
    public function verify(InternalRequestInterface $request);

    /**
     * @param InternalRequestInterface $request
     *
     * @return float
     */
    public function delay(InternalRequestInterface $request);
}
