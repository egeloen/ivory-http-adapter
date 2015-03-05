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

use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Event\Retry\Strategy\ExponentialDelayedRetryStrategy;
use Ivory\HttpAdapter\Event\Retry\Strategy\LimitedRetryStrategy;
use Ivory\HttpAdapter\Event\Retry\Strategy\RetryStrategyInterface;

/**
 * Retry.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Retry implements RetryInterface
{
    /** @var \Ivory\HttpAdapter\Event\Retry\Strategy\RetryStrategyInterface */
    private $strategy;

    /**
     * Creates a retry.
     *
     * @param \Ivory\HttpAdapter\Event\Retry\Strategy\RetryStrategyInterface|null $strategy The strategy.
     */
    public function __construct(RetryStrategyInterface $strategy = null)
    {
        $this->setStrategy($strategy ?: new LimitedRetryStrategy(3, new ExponentialDelayedRetryStrategy()));
    }

    /**
     * {@inheritdoc}
     */
    public function getStrategy()
    {
        return $this->strategy;
    }

    /**
     * {@inheritdoc}
     */
    public function setStrategy(RetryStrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * {@inheritdoc}
     */
    public function retry(InternalRequestInterface $internalRequest, $wait = true)
    {
        if (!$this->strategy->verify($internalRequest)) {
            return false;
        }

        if ($wait && ($delay = $this->strategy->delay($internalRequest)) > 0) {
            usleep($delay * 1000000);
        }

        return $internalRequest->withParameter(
            self::RETRY_COUNT,
            $internalRequest->getParameter(self::RETRY_COUNT) + 1
        );
    }
}
