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
class CallbackRetryStrategy extends AbstractRetryStrategyChain
{
    /**
     * @var callable|null
     */
    private $verifyCallback;

    /**
     * @var callable|null
     */
    private $delayCallback;

    /**
     * @param callable|null                    $verifyCallback
     * @param callable|null                    $delayCallback
     * @param RetryStrategyChainInterface|null $next
     */
    public function __construct($verifyCallback = null, $delayCallback = null, RetryStrategyChainInterface $next = null)
    {
        parent::__construct($next);

        $this->setVerifyCallback($verifyCallback);
        $this->setDelayCallback($delayCallback);
    }

    /**
     * @return bool
     */
    public function hasVerifyCallback()
    {
        return $this->verifyCallback !== null;
    }

    /**
     * @return callable|null
     */
    public function getVerifyCallback()
    {
        return $this->verifyCallback;
    }

    /**
     * @param callable|null $verifyCallback
     */
    public function setVerifyCallback($verifyCallback)
    {
        $this->verifyCallback = $verifyCallback;
    }

    /**
     * @return bool
     */
    public function hasDelayCallback()
    {
        return $this->delayCallback !== null;
    }

    /**
     * @return callable|null
     */
    public function getDelayCallback()
    {
        return $this->delayCallback;
    }

    /**
     * @param callable|null $delayCallback
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
