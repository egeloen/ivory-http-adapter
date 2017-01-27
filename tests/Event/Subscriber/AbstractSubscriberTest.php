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
use Ivory\HttpAdapter\Event\MultiRequestCreatedEvent;
use Ivory\HttpAdapter\Event\MultiRequestErroredEvent;
use Ivory\HttpAdapter\Event\MultiRequestSentEvent;
use Ivory\HttpAdapter\Event\RequestCreatedEvent;
use Ivory\HttpAdapter\Event\RequestErroredEvent;
use Ivory\HttpAdapter\Event\RequestSentEvent;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\MessageFactoryInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Ivory\HttpAdapter\MultiHttpAdapterException;
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractSubscriberTest extends AbstractTestCase
{
    /**
     * @param HttpAdapterInterface|null     $httpAdapter
     * @param InternalRequestInterface|null $request
     *
     * @return RequestCreatedEvent
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
     * @param HttpAdapterInterface|null     $httpAdapter
     * @param InternalRequestInterface|null $request
     * @param ResponseInterface|null        $response
     *
     * @return RequestSentEvent
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
     * @param HttpAdapterInterface|null $httpAdapter
     * @param HttpAdapterException|null $exception
     *
     * @return RequestErroredEvent
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
     * @param HttpAdapterInterface|null $httpAdapter
     * @param array                     $requests
     *
     * @return MultiRequestCreatedEvent
     */
    protected function createMultiRequestCreatedEvent(HttpAdapterInterface $httpAdapter = null, array $requests = [])
    {
        return new MultiRequestCreatedEvent($httpAdapter ?: $this->createHttpAdapterMock(), $requests);
    }

    /**
     * @param HttpAdapterInterface|null $httpAdapter
     * @param array                     $responses
     *
     * @return MultiRequestSentEvent
     */
    protected function createMultiRequestSentEvent(HttpAdapterInterface $httpAdapter = null, array $responses = [])
    {
        return new MultiRequestSentEvent($httpAdapter ?: $this->createHttpAdapterMock(), $responses);
    }

    /**
     * @param HttpAdapterInterface|null $httpAdapter
     * @param array                     $exceptions
     * @param array                     $responses
     *
     * @return MultiRequestErroredEvent
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
     * @param ConfigurationInterface|null $configuration
     *
     * @return HttpAdapterInterface|\PHPUnit_Framework_MockObject_MockObject
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
     * @param MessageFactoryInterface|null $messageFactory
     *
     * @return ConfigurationInterface|\PHPUnit_Framework_MockObject_MockObject
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
     * @return MessageFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMessageFactoryMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\MessageFactoryInterface');
    }

    /**
     * @return InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createRequestMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
    }

    /**
     * @return ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createResponseMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }

    /**
     * @param InternalRequestInterface|null $internalRequest
     * @param ResponseInterface|null        $response
     *
     * @return HttpAdapterException|\PHPUnit_Framework_MockObject_MockObject
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
     * @param array $exceptions
     * @param array $responses
     *
     * @return MultiHttpAdapterException|\PHPUnit_Framework_MockObject_MockObject
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
