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
 * Limited retry strategy.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class LimitedRetryStrategy extends AbstractRetryStrategyChain
{
    /** @var integer */
    protected $limit;

    /**
     * Creates a limited retry strategy.
     *
     * @param integer                                                         $limit The limit.
     * @param \Ivory\HttpAdapter\Event\Retry\RetryStrategyChainInterface|null $next  The next retry strategy chain.
     */
    public function __construct($limit = 3, RetryStrategyChainInterface $next = null)
    {
        parent::__construct($next);

        $this->setLimit($limit);
    }

    /**
     * Gets the limit.
     *
     * @return integer The limit.
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Sets the limit.
     *
     * @param integer $limit The limit.
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * {@inheritdoc}
     */
    protected function doVerify(InternalRequestInterface $request, HttpAdapterException $exception)
    {
        return $request->getParameter(RetrySubscriber::RETRY_COUNT) > $this->limit;
    }
}
