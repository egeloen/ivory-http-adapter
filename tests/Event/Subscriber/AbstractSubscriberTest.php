<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\Subscriber;

use Ivory\HttpAdapter\Event\ExceptionEvent;
use Ivory\HttpAdapter\Event\PostSendEvent;
use Ivory\HttpAdapter\Event\PreSendEvent;
use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * Abstract subscriber test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates a pre send event.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface|null             $httpAdapter The http adapter.
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface|null $request     The request.
     *
     * @return \Ivory\HttpAdapter\Event\PreSendEvent The pre send event.
     */
    protected function createPreSendEvent(
        HttpAdapterInterface $httpAdapter = null,
        InternalRequestInterface $request = null
    ) {
        return new PreSendEvent(
            $httpAdapter ?: $this->createHttpAdapterMock(),
            $request ?: $this->createRequestMock()
        );
    }

    /**
     * Creates a post send event.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface|null             $httpAdapter The http adapter.
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface|null $request     The request.
     * @param \Ivory\HttpAdapter\Message\ResponseInterface|null        $response    The response.
     *
     * @return \Ivory\HttpAdapter\Event\PostSendEvent The post send event.
     */
    protected function createPostSendEvent(
        HttpAdapterInterface $httpAdapter = null,
        InternalRequestInterface $request = null,
        ResponseInterface $response = null
    ) {
        return new PostSendEvent(
            $httpAdapter ?: $this->createHttpAdapterMock(),
            $request ?: $this->createRequestMock(),
            $response ?: $this->createResponseMock()
        );
    }

    /**
     * Creates an exception event.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface|null             $httpAdapter The http adapter.
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface|null $request     The request.
     * @param \Ivory\HttpAdapter\HttpAdapterException|null             $exception   The exception.
     *
     * @return \Ivory\HttpAdapter\Event\ExceptionEvent The exception event.
     */
    protected function createExceptionEvent(
        HttpAdapterInterface $httpAdapter = null,
        InternalRequestInterface $request = null,
        HttpAdapterException $exception = null
    ) {
        return new ExceptionEvent(
            $httpAdapter ?: $this->createHttpAdapterMock(),
            $request ?: $this->createRequestMock(),
            $exception ?: $this->createExceptionMock()
        );
    }

    /**
     * Creates an http adapter mock.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterInterface|\PHPUnit_Framework_MockObject_MockObject The http adapter mock.
     */
    protected function createHttpAdapterMock()
    {
        $httpAdapter = $this->getMock('Ivory\HttpAdapter\HttpAdapterInterface');
        $httpAdapter
            ->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue($this->createConfigurationMock()));

        return $httpAdapter;
    }

    /**
     * Creates a configuration mock.
     *
     * @return \Ivory\HttpAdapter\ConfigurationInterface|\PHPUnit_Framework_MockObject_MockObject The configuration mock.
     */
    protected function createConfigurationMock()
    {
        return $this->getMock('Ivory\HttpAdapter\ConfigurationInterface');
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
     * Creates a response mock.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject The response mock.
     */
    protected function createResponseMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\ResponseInterface');
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
