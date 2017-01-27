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
abstract class AbstractDelayedRetryStrategy extends AbstractRetryStrategyChain
{
    /**
     * @var float
     */
    private $delay;

    /**
     * @param float                            $delay
     * @param RetryStrategyChainInterface|null $next
     */
    public function __construct($delay = 5.0, RetryStrategyChainInterface $next = null)
    {
        parent::__construct($next);

        $this->setDelay($delay);
    }

    /**
     * @return float
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * @param float $delay
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;
    }
}
