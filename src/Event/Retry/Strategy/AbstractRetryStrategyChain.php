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
 * Abstract retry strategy chain.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractRetryStrategyChain implements RetryStrategyChainInterface
{
    /** @var \Ivory\HttpAdapter\Event\Retry\Strategy\RetryStrategyChainInterface */
    private $next;

    /**
     * Creates a chained retry strategy.
     *
     * @param \Ivory\HttpAdapter\Event\Retry\Strategy\RetryStrategyChainInterface|null $next The next chained retry strategy.
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
     * Does the retry verification.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request The request.
     *
     * @return boolean TRUE if it should retry to send the request else FALSE.
     */
    protected function doVerify(InternalRequestInterface $request)
    {
        return true;
    }

    /**
     * Does the retry delay.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request The request.
     *
     * @return integer The delay before retrying to send the request.
     */
    protected function doDelay(InternalRequestInterface $request)
    {
        return 0;
    }
}
