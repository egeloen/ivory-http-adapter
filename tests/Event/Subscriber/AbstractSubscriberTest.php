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
use Ivory\HttpAdapter\Event\MultiExceptionEvent;
use Ivory\HttpAdapter\Event\MultiPostSendEvent;
use Ivory\HttpAdapter\Event\MultiPreSendEvent;
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
     * @param \Ivory\HttpAdapter\HttpAdapterInterface|null $httpAdapter The http adapter.
     * @param \Ivory\HttpAdapter\HttpAdapterException|null $exception   The exception.
     *
     * @return \Ivory\HttpAdapter\Event\ExceptionEvent The exception event.
     */
    protected function createExceptionEvent(
        HttpAdapterInterface $httpAdapter = null,
        HttpAdapterException $exception = null
    ) {
        return new ExceptionEvent(
            $httpAdapter ?: $this->createHttpAdapterMock(),
            $exception ?: $this->createExceptionMock()
        );
    }

    /**
     * Creates a multi pre send event.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface|null $httpAdapter The http adapter.
     * @param array                                        $requests    The requuests.
     *
     * @return \Ivory\HttpAdapter\Event\MultiPreSendEvent The multi pre send event.
     */
    protected function createMultiPreSendEvent(HttpAdapterInterface $httpAdapter = null, array $requests = array())
    {
        return new MultiPreSendEvent($httpAdapter ?: $this->createHttpAdapterMock(), $requests);
    }

    /**
     * Creates a multi post send event.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface|null $httpAdapter The http adapter.
     * @param array                                        $responses   The responses.
     *
     * @return \Ivory\HttpAdapter\Event\MultiPostSendEvent The multi post send event.
     */
    protected function createMultiPostSendEvent(HttpAdapterInterface $httpAdapter = null, array $responses = array())
    {
        return new MultiPostSendEvent($httpAdapter ?: $this->createHttpAdapterMock(), $responses);
    }

    /**
     * Creates a multi exception event.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface|null $httpAdapter The http adapter.
     * @param array                                        $exceptions  The exceptions.
     * @param array                                        $responses   The responses.
     *
     * @return \Ivory\HttpAdapter\Event\MultiExceptionEvent The multi exception event.
     */
    protected function createMultiExceptionEvent(
        HttpAdapterInterface $httpAdapter = null,
        array $exceptions = array(),
        array $responses = array()
    ) {
        return new MultiExceptionEvent(
            $httpAdapter ?: $this->createHttpAdapterMock(),
            $this->createMultiExceptionMock($exceptions, $responses)
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
            ->method('getName')
            ->will($this->returnValue('http_adapter'));

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
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface|null $internalRequest The internal request.
     * @param \Ivory\HttpAdapter\Message\ResponseInterface|null        $response        The response.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException|\PHPUnit_Framework_MockObject_MockObject The exception mock.
     */
    protected function createExceptionMock(
        InternalRequestInterface $internalRequest = null,
        ResponseInterface $response = null
    ) {
        $exception = $this->getMock('Ivory\HttpAdapter\HttpAdapterException');

        if ($internalRequest === null) {
            $internalRequest = $this->createRequestMock();
        }

        $exception
            ->expects($this->any())
            ->method('hasRequest')
            ->will($this->returnValue(true));

        $exception
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($internalRequest));

        if ($response !== null) {
            $exception
                ->expects($this->any())
                ->method('hasResponse')
                ->will($this->returnValue(true));

            $exception
                ->expects($this->any())
                ->method('getResponse')
                ->will($this->returnValue($response));
        }

        return $exception;
    }

    /**
     * Creates a multi exception mock.
     *
     * @param array $exceptions The exceptions.
     * @param array $responses  The responses.
     *
     * @return \Ivory\HttpAdapter\MultiHttpAdapterException|\PHPUnit_Framework_MockObject_MockObject The multi exception mock.
     */
    protected function createMultiExceptionMock(array $exceptions = array(), array $responses = array())
    {
        $exception = $this->getMock('Ivory\HttpAdapter\MultiHttpAdapterException');
        $exception
            ->expects($this->any())
            ->method('hasExceptions')
            ->will($this->returnValue(!empty($exceptions)));

        $exception
            ->expects($this->any())
            ->method('getExceptions')
            ->will($this->returnValue($exceptions));

        $exception
            ->expects($this->any())
            ->method('hasResponses')
            ->will($this->returnValue(!empty($responses)));

        $exception
            ->expects($this->any())
            ->method('getResponses')
            ->will($this->returnValue($responses));

        return $exception;
    }
}
