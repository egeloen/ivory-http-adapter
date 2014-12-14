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
use Ivory\HttpAdapter\HttpAdapterInterface;

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
     * Creates a retry subscriber.
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
    public function retry(InternalRequestInterface $internalRequest, HttpAdapterInterface $httpAdapter)
    {
        if (!$this->strategy->verify($internalRequest)) {
            $internalRequest->setParameter(
                self::RETRY_COUNT,
                (int) $internalRequest->getParameter(self::RETRY_COUNT)
            );

            return;
        }

        if (($delay = $this->strategy->delay($internalRequest)) > 0) {
            usleep($delay * 1000000);
        }

        $internalRequest->setParameter(self::RETRY_COUNT, $internalRequest->getParameter(self::RETRY_COUNT) + 1);

        return $httpAdapter->sendRequest($internalRequest);
    }
}