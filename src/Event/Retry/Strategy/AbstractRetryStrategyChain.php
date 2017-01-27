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

use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractRetryStrategyChain implements RetryStrategyChainInterface
{
    /**
     * @var RetryStrategyChainInterface
     */
    private $next;

    /**
     * @param RetryStrategyChainInterface|null $next
     */
    public function __construct(RetryStrategyChainInterface $next = null)
    {
        $this->setNext($next);
    }

    /**
     * {@inheritdoc}
     */
    public function hasNext()
    {
        return $this->next !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * {@inheritdoc}
     */
    public function setNext(RetryStrategyChainInterface $next = null)
    {
        $this->next = $next;
    }

    /**
     * {@inheritdoc}
     */
    public function verify(InternalRequestInterface $request)
    {
        $verify = $this->doVerify($request);

        if ($verify && $this->hasNext()) {
            return $this->next->verify($request);
        }

        return $verify;
    }

    /**
     * {@inheritdoc}
     */
    public function delay(InternalRequestInterface $request)
    {
        $delay = $this->doDelay($request);

        if ($this->hasNext() && (($nextDelay = $this->next->delay($request)) > $delay)) {
            return $nextDelay;
        }

        return $delay;
    }

    /**
     * @param InternalRequestInterface $request
     *
     * @return bool
     */
    protected function doVerify(InternalRequestInterface $request)
    {
        return true;
    }

    /**
     * @param InternalRequestInterface $request
     *
     * @return int
     */
    protected function doDelay(InternalRequestInterface $request)
    {
        return 0;
    }
}
