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

use Ivory\HttpAdapter\Event\Retry\Strategy\RetryStrategyInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface RetryInterface
{
    const RETRY_COUNT = 'retry_count';

    /**
     * @return RetryStrategyInterface
     */
    public function getStrategy();

    /**
     * @param RetryStrategyInterface $strategy
     */
    public function setStrategy(RetryStrategyInterface $strategy);

    /**
     * @param InternalRequestInterface $internalRequest
     * @param bool                     $wait
     *
     * @return InternalRequestInterface|bool
     */
    public function retry(InternalRequestInterface $internalRequest, $wait = true);
}
