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
 * Linear delayed retry strategy.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class LinearDelayedRetryStrategy extends AbstractDelayedRetryStrategy
{
    /**
     * {@inheritdoc}
     */
    protected function doDelay(InternalRequestInterface $request)
    {
        return $this->getDelay() * $request->getParameter(RetryInterface::RETRY_COUNT);
    }
}
