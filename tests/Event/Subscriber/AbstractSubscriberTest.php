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

use Ivory\HttpAdapter\ConfigurationInterface;
use Ivory\HttpAdapter\Event\RequestErroredEvent;
use Ivory\HttpAdapter\Event\MultiRequestErroredEvent;
use Ivory\HttpAdapter\Event\MultiRequestSentEvent;
use Ivory\HttpAdapter\Event\MultiRequestCreatedEvent;
use Ivory\HttpAdapter\Event\RequestSentEvent;
use Ivory\HttpAdapter\Event\RequestCreatedEvent;
use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\MessageFactoryInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * Abstract subscriber test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractSubscriberTest extends AbstractTestCase 
{
    /**
     * Creates a pre send event.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface|null             $httpAdapter The http adapter.
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface|null $request     The request.
     *
     * @return \Ivory\HttpAdapter\Event\RequestCreatedEvent The request created event.
     */
    protected function createRequestCreatedEvent(
        HttpAdapterInterface $httpAdapter = null,
        InternalRequestInterface $request = null
    ) {
        return new RequestCreatedEvent(
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
     * @return \Ivory\HttpAdapter\Event\RequestSentEvent The request sent event.
     */
    protected function createRequestSentEvent(
        HttpAdapterInterface $httpAdapter = null,
        InternalRequestInterface $request = null,
        ResponseInterface $response = null
    ) {
        return new RequestSentEvent(
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
     * @return \Ivory\HttpAdapter\Event\RequestErroredEvent The request errored event.
     */
    protected function createRequestErroredEvent(
        HttpAdapterInterface $httpAdapter = null,
        HttpAdapterException $exception = null
    ) {
        return new RequestErroredEvent(
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
     * @return \Ivory\HttpAdapter\Event\MultiRequestCreatedEvent The multi request created event.
     */
    protected function createMultiRequestCreatedEvent(HttpAdapterInterface $httpAdapter = null, array $requests = [])
    {
        return new MultiRequestCreatedEvent($httpAdapter ?: $this->createHttpAdapterMock(), $requests);
    }

    /**
     * Creates a multi post send event.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface|null $httpAdapter The http adapter.
     * @param array                                        $responses   The responses.
     *
     * @return \Ivory\HttpAdapter\Event\MultiRequestSentEvent The multi request sent event.
     */
    protected function createMultiRequestSentEvent(HttpAdapterInterface $httpAdapter = null, array $responses = [])
    {
        return new MultiRequestSentEvent($httpAdapter ?: $this->createHttpAdapterMock(), $responses);
    }

    /**
     * Creates a multi exception event.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface|null $httpAdapter The http adapter.
     * @param array                                        $exceptions  The exceptions.
     * @param array                                        $responses   The responses.
     *
     * @return \Ivory\HttpAdapter\Event\MultiRequestErroredEvent The multi request errored event.
     */
    protected function createMultiRequestErroredEvent(
        HttpAdapterInterface $httpAdapter = null,
        array $exceptions = [],
        array $responses = []
    ) {
        $event = new MultiRequestErroredEvent($httpAdapter ?: $this->createHttpAdapterMock(), $exceptions);
        $event->setResponses($responses);

        return $event;
    }

    /**
     * Creates an http adapter mock.
     *
     * @param ConfigurationInterface|null $configuration The configuration.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterInterface|\PHPUnit_Framework_MockObject_MockObject The http adapter mock.
     */
    protected function createHttpAdapterMock(ConfigurationInterface $configuration = null)
    {
        $httpAdapter = $this->createMock('Ivory\HttpAdapter\HttpAdapterInterface');
        $httpAdapter
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('http_adapter'));

        $httpAdapter
            ->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue($configuration ?: $this->createConfigurationMock()));

        return $httpAdapter;
    }

    /**
     * Creates a configuration mock.
     *
     * @param \Ivory\HttpAdapter\Message\MessageFactoryInterface|null $messageFactory The message factory.
     *
     * @return \Ivory\HttpAdapter\ConfigurationInterface|\PHPUnit_Framework_MockObject_MockObject The configuration mock.
     */
    protected function createConfigurationMock(MessageFactoryInterface $messageFactory = null)
    {
        $configuration = $this->createMock('Ivory\HttpAdapter\ConfigurationInterface');
        $configuration
            ->expects($this->any())
            ->method('getMessageFactory')
            ->will($this->returnValue($messageFactory ?: $this->createMessageFactoryMock()));

        return $configuration;
    }

    /**
     * Creates a message factory mock.
     *
     * @return \Ivory\HttpAdapter\Message\MessageFactoryInterface|\PHPUnit_Framework_MockObject_MockObject The message factory mock.
     */
    protected function createMessageFactoryMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\MessageFactoryInterface');
    }

    /**
     * Creates a request mock.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject The request mock.
     */
    protected function createRequestMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
    }

    /**
     * Creates a response mock.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject The response mock.
     */
    protected function createResponseMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\ResponseInterface');
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
        $exception = $this->createMock('Ivory\HttpAdapter\HttpAdapterException');

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
    protected function createMultiExceptionMock(array $exceptions = [], array $responses = [])
    {
        $exception = $this->createMock('Ivory\HttpAdapter\MultiHttpAdapterException');
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
