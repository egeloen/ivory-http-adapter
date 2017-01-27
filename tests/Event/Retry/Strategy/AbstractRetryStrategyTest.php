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

use Ivory\HttpAdapter\Event\Retry\Strategy\RetryStrategyChainInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractRetryStrategyTest extends AbstractTestCase
{
    /**
     * @return RetryStrategyChainInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createRetryStrategyChainMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Event\Retry\Strategy\RetryStrategyChainInterface');
    }

    /**
     * @return InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createRequestMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
    }
}
