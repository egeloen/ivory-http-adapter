<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\Retry;

use Ivory\HttpAdapter\Event\Subscriber\RetrySubscriber;
use Ivory\HttpAdapter\HttpAdapterException;
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
    protected function doDelay(InternalRequestInterface $request, HttpAdapterException $exception)
    {
        return pow(2, $request->getParameter(RetrySubscriber::RETRY_COUNT));
    }
}
