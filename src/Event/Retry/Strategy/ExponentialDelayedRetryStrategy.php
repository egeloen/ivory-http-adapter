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

use Ivory\HttpAdapter\Event\Retry\RetryInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * Exponential retry strategy.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ExponentialDelayedRetryStrategy extends AbstractRetryStrategyChain
{
    /**
     * {@inheritdoc}
     */
    protected function doDelay(InternalRequestInterface $request)
    {
        return pow(2, $request->getParameter(RetryInterface::RETRY_COUNT));
    }
}
