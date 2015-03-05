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

/**
 * Retry strategy chain.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface RetryStrategyChainInterface extends RetryStrategyInterface
{
    /**
     * Checks if there is a next chained retry strategy.
     *
     * @return boolean TRUE if there is a next chained retry strategy else FALSE.
     */
    public function hasNext();

    /**
     * Gets the next chained retry strategy.
     *
     * @return \Ivory\HttpAdapter\Event\Retry\Strategy\RetryStrategyChainInterface|null The next retry strategy chain.
     */
    public function getNext();

    /**
     * Sets the next chained retry strategy.
     *
     * @param \Ivory\HttpAdapter\Event\Retry\Strategy\RetryStrategyChainInterface|null $next The next retry strategy chain.
     */
    public function setNext(RetryStrategyChainInterface $next = null);
}
