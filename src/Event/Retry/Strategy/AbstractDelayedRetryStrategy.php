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
 * Abstract delayed retry strategy.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractDelayedRetryStrategy extends AbstractRetryStrategyChain
{
    /** @var float */
    private $delay;

    /**
     * Creates a delayed retry strategy.
     *
     * @param float                                                                    $delay The delay.
     * @param \Ivory\HttpAdapter\Event\Retry\Strategy\RetryStrategyChainInterface|null $next  The next retry strategy chain.
     */
    public function __construct($delay = 5, RetryStrategyChainInterface $next = null)
    {
        parent::__construct($next);

        $this->setDelay($delay);
    }

    /**
     * Gets the delay.
     *
     * @return float The delay.
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * Sets the delay.
     *
     * @param float $delay The delay.
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;
    }
}
