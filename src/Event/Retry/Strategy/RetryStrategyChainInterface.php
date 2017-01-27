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
 * @author GeLo <geloen.eric@gmail.com>
 */
interface RetryStrategyChainInterface extends RetryStrategyInterface
{
    /**
     * @return bool
     */
    public function hasNext();

    /**
     * @return RetryStrategyChainInterface|null
     */
    public function getNext();

    /**
     * @param RetryStrategyChainInterface|null $next
     */
    public function setNext(RetryStrategyChainInterface $next = null);
}
