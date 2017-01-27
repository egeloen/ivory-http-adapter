<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\Retry\Strategy;

use Ivory\HttpAdapter\Event\Retry\Strategy\CallbackRetryStrategy;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CallbackRetryStrategyTest extends AbstractRetryStrategyTest
{
    /**
     * @var CallbackRetryStrategy
     */
    private $callbackRetryStrategy;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->callbackRetryStrategy = new CallbackRetryStrategy();
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Retry\Strategy\AbstractRetryStrategyChain',
            $this->callbackRetryStrategy
        );

        $this->assertFalse($this->callbackRetryStrategy->hasVerifyCallback());
        $this->assertNull($this->callbackRetryStrategy->getVerifyCallback());

        $this->assertFalse($this->callbackRetryStrategy->hasDelayCallback());
        $this->assertNull($this->callbackRetryStrategy->getDelayCallback());

        $this->assertFalse($this->callbackRetryStrategy->hasNext());
        $this->assertNull($this->callbackRetryStrategy->getNext());
    }

    public function testInitialState()
    {
        $this->callbackRetryStrategy = new CallbackRetryStrategy(
            $verifyCallback = function () {
                return false;
            },
            $delayCallback = function () {
                return 1;
            },
            $next = $this->createRetryStrategyChainMock()
        );

        $this->assertTrue($this->callbackRetryStrategy->hasVerifyCallback());
        $this->assertSame($verifyCallback, $this->callbackRetryStrategy->getVerifyCallback());

        $this->assertTrue($this->callbackRetryStrategy->hasDelayCallback());
        $this->assertSame($delayCallback, $this->callbackRetryStrategy->getDelayCallback());

        $this->assertTrue($this->callbackRetryStrategy->hasNext());
        $this->assertSame($next, $this->callbackRetryStrategy->getNext());
    }

    public function testSetVerifyCallback()
    {
        $this->callbackRetryStrategy->setVerifyCallback($verifyCallback = function () {
            return false;
        });

        $this->assertTrue($this->callbackRetryStrategy->hasVerifyCallback());
        $this->assertSame($verifyCallback, $this->callbackRetryStrategy->getVerifyCallback());
    }

    public function testSetDelayCallback()
    {
        $this->callbackRetryStrategy->setDelayCallback($delayCallback = function () {
            return 1;
        });

        $this->assertTrue($this->callbackRetryStrategy->hasDelayCallback());
        $this->assertSame($delayCallback, $this->callbackRetryStrategy->getDelayCallback());
    }

    public function testVerifyWithoutCallback()
    {
        $this->assertTrue($this->callbackRetryStrategy->verify($this->createRequestMock()));
    }

    public function testVerifyWithCallback()
    {
        $that = $this;
        $request = $this->createRequestMock();

        $verifyCallback = function ($callbackRequest) use ($that, $request) {
            $that->assertSame($request, $callbackRequest);

            return false;
        };

        $this->callbackRetryStrategy->setVerifyCallback($verifyCallback);

        $this->assertFalse($this->callbackRetryStrategy->verify($request));
    }

    public function testDelayWithoutCallback()
    {
        $this->assertSame(0, $this->callbackRetryStrategy->delay($this->createRequestMock()));
    }

    public function testDelayWithCallback()
    {
        $that = $this;
        $request = $this->createRequestMock();

        $delayCallback = function ($callbackRequest) use ($that, $request) {
            $that->assertSame($request, $callbackRequest);

            return 1;
        };

        $this->callbackRetryStrategy->setDelayCallback($delayCallback);

        $this->assertSame(1, $this->callbackRetryStrategy->delay($request));
    }
}
