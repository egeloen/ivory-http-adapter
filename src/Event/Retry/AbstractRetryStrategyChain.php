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

use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * Abstract retry strategy chain.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractRetryStrategyChain implements RetryStrategyChainInterface
{
    /** @var \Ivory\HttpAdapter\Event\Retry\RetryStrategyChainInterface */
    protected $next;

    /**
     * Creates a chained retry strategy.
     *
     * @param \Ivory\HttpAdapter\Event\Retry\RetryStrategyChainInterface|null $next The next chained retry strategy.
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
    public function verify(InternalRequestInterface $request, HttpAdapterException $exception)
    {
        $verify = $this->doVerify($request, $exception);

        if ($verify && $this->hasNext()) {
            $verify = $verify && $this->next->verify($request, $exception);
        }

        return $verify;
    }

    /**
     * {@inheritdoc}
     */
    public function delay(InternalRequestInterface $request, HttpAdapterException $exception)
    {
        $delay = $this->doDelay($request, $exception);

        if ($this->hasNext() && (($nextDelay = $this->next->delay($request, $exception)) > $delay)) {
            $delay = $nextDelay;
        }

        return $delay;
    }

    /**
     * Does the retry verification.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request   The request.
     * @param \Ivory\HttpAdapter\HttpAdapterException             $exception The exception.
     *
     * @return boolean TRUE if it should retry to send the request else FALSE.
     */
    protected function doVerify(InternalRequestInterface $request, HttpAdapterException $exception)
    {
        return true;
    }

    /**
     * Does the retry delay.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request   The request.
     * @param \Ivory\HttpAdapter\HttpAdapterException             $exception The exception.
     *
     * @return integer The delay before retrying to send the request.
     */
    protected function doDelay(InternalRequestInterface $request, HttpAdapterException $exception)
    {
        return 0;
    }
}
