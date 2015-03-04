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
 * Retry.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface RetryInterface
{
    /** @const string The retry count parameter. */
    const RETRY_COUNT = 'retry_count';

    /**
     * Gets the strategy.
     *
     * @return \Ivory\HttpAdapter\Event\Retry\Strategy\RetryStrategyInterface The strategy.
     */
    public function getStrategy();

    /**
     * Sets the strategy.
     *
     * @param \Ivory\HttpAdapter\Event\Retry\Strategy\RetryStrategyInterface $strategy The strategy.
     */
    public function setStrategy(RetryStrategyInterface $strategy);

    /**
     * Checks if it should retry a request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     * @param boolean                                             $wait            TRUE if the delay should be considered else FALSE.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|boolean The retry request or FALSE if it should not retry it.
     */
    public function retry(InternalRequestInterface $internalRequest, $wait = true);
}
