<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\Retry;

/**
 * Abstract retry strategy test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractRetryStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates a retry strategy chain mock.
     *
     * @return \Ivory\HttpAdapter\Event\Retry\RetryStrategyChainInterface|\PHPUnit_Framework_MockObject_MockObject The retry strategy chain mock.
     */
    protected function createRetryStrategyChainMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Event\Retry\RetryStrategyChainInterface');
    }

    /**
     * Creates a request mock.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject The request mock.
     */
    protected function createRequestMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
    }

    /**
     * Creates an exception mock.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException|\PHPUnit_Framework_MockObject_MockObject The exception mock.
     */
    protected function createExceptionMock()
    {
        return $this->getMock('Ivory\HttpAdapter\HttpAdapterException');
    }
}
