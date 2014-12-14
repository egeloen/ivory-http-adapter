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
 * Callback retry strategy.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CallbackRetryStrategy extends AbstractRetryStrategyChain
{
    /** @var callable|null */
    private $verifyCallback;

    /** @var callable|null */
    private $delayCallback;

    /**
     * Creates a callback retry strategy.
     *
     * @param callable|null                                                            $verifyCallback The verify callback.
     * @param callable|null                                                            $delayCallback  The delay callback.
     * @param \Ivory\HttpAdapter\Event\Retry\Strategy\RetryStrategyChainInterface|null $next           The next retry strategy chain.
     */
    public function __construct($verifyCallback = null, $delayCallback = null, RetryStrategyChainInterface $next = null)
    {
        parent::__construct($next);

        $this->setVerifyCallback($verifyCallback);
        $this->setDelayCallback($delayCallback);
    }

    /**
     * Checks if there is a verify callback.
     *
     * @return boolean TRUE if there is a verify callback else FALSE.
     */
    public function hasVerifyCallback()
    {
        return $this->verifyCallback !== null;
    }

    /**
     * Gets the verify callback.
     *
     * @return callable|null The verify callback.
     */
    public function getVerifyCallback()
    {
        return $this->verifyCallback;
    }

    /**
     * Sets the verify callback.
     *
     * @param callable|null $verifyCallback The verify callback.
     */
    public function setVerifyCallback($verifyCallback)
    {
        $this->verifyCallback = $verifyCallback;
    }

    /**
     * Checks if there is a delay callback.
     *
     * @return boolean TRUE if there is a delay callback else FALSE.
     */
    public function hasDelayCallback()
    {
        return $this->delayCallback !== null;
    }

    /**
     * Gets the delay callback.
     *
     * @return callable|null The delay callback.
     */
    public function getDelayCallback()
    {
        return $this->delayCallback;
    }

    /**
     * Sets the delay callback.
     *
     * @param callable|null $delayCallback The delay callback.
     */
    public function setDelayCallback($delayCallback)
    {
        $this->delayCallback = $delayCallback;
    }

    /**
     * {@inheritdoc}
     */
    protected function doVerify(InternalRequestInterface $request)
    {
        if ($this->hasVerifyCallback()) {
            return call_user_func($this->verifyCallback, $request);
        }

        return parent::doVerify($request);
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelay(InternalRequestInterface $request)
    {
        if ($this->hasDelayCallback()) {
            return call_user_func($this->delayCallback, $request);
        }

        return parent::doDelay($request);
    }
}
