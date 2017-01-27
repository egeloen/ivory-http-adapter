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
 * @author GeLo <geloen.eric@gmail.com>
 */
class LimitedRetryStrategy extends AbstractRetryStrategyChain
{
    /**
     * @var int
     */
    private $limit;

    /**
     * @param int                              $limit
     * @param RetryStrategyChainInterface|null $next
     */
    public function __construct($limit = 3, RetryStrategyChainInterface $next = null)
    {
        parent::__construct($next);

        $this->setLimit($limit);
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * {@inheritdoc}
     */
    protected function doVerify(InternalRequestInterface $request)
    {
        return $request->getParameter(RetryInterface::RETRY_COUNT) < $this->limit;
    }
}
