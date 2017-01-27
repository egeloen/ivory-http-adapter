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

use Ivory\HttpAdapter\Event\Events;
use Ivory\HttpAdapter\Event\Retry\RetryInterface;
use Ivory\HttpAdapter\Event\Subscriber\RetrySubscriber;
use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RetrySubscriberTest extends AbstractSubscriberTest
{
    /**
     * @var RetrySubscriber
     */
    private $retrySubscriber;

    /**
     * @var RetryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $retry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->retrySubscriber = new RetrySubscriber($this->retry = $this->createRetryMock());
    }

    public function testDefaultState()
    {
        $this->retrySubscriber = new RetrySubscriber();

        $this->assertInstanceOf('Ivory\HttpAdapter\Event\Retry\Retry', $this->retrySubscriber->getRetry());
    }

    public function testInitialState()
    {
        $this->assertSame($this->retry, $this->retrySubscriber->getRetry());
    }

    public function testSubscribedEvents()
    {
        $events = RetrySubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::REQUEST_ERRORED, $events);
        $this->assertSame(['onRequestErrored', 0], $events[Events::REQUEST_ERRORED]);

        $this->assertArrayHasKey(Events::MULTI_REQUEST_ERRORED, $events);
        $this->assertSame(['onMultiResponseErrored', 0], $events[Events::MULTI_REQUEST_ERRORED]);
    }

    public function testRequestErroredEventRetried()
    {
        $this->retry
            ->expects($this->once())
            ->method('retry')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue($retryRequest = $this->createRequestMock()));

        $httpAdapter = $this->createHttpAdapterMock();
        $httpAdapter
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->identicalTo($retryRequest))
            ->will($this->returnValue($retryResponse = $this->createResponseMock()));

        $exception = $this->createExceptionMock($request);
        $exception
            ->expects($this->once())
            ->method('setRequest')
            ->with($this->identicalTo($retryRequest));

        $this->retrySubscriber->onRequestErrored($event = $this->createRequestErroredEvent($httpAdapter, $exception));

        $this->assertSame($retryResponse, $event->getResponse());
    }

    public function testRequestErroredEventRetriedThrowException()
    {
        $this->retry
            ->expects($this->once())
            ->method('retry')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue($retryRequest = $this->createRequestMock()));

        $httpAdapter = $this->createHttpAdapterMock();
        $httpAdapter
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->identicalTo($retryRequest))
            ->will($this->throwException($exception = $this->createExceptionMock()));

        $this->retrySubscriber->onRequestErrored(
            $event = $this->createRequestErroredEvent($httpAdapter, $this->createExceptionMock($request))
        );

        $this->assertFalse($event->hasResponse());
        $this->assertSame($exception, $event->getException());
    }

    public function testRequestErroredEventNotRetried()
    {
        $this->retry
            ->expects($this->once())
            ->method('retry')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue(false));

        $httpAdapter = $this->createHttpAdapterMock();
        $httpAdapter
            ->expects($this->never())
            ->method('sendRequest');

        $this->retrySubscriber->onRequestErrored($event = $this->createRequestErroredEvent(
            $httpAdapter,
            $this->createExceptionMock($request)
        ));

        $this->assertNull($event->getResponse());
    }

    public function testMultiRequestErroredEventRetried()
    {
        $requests = [
            $request1 = $this->createRequestMock(),
            $request2 = $this->createRequestMock(),
        ];

        $responses = [
            $response1 = $this->createResponseMock($request1),
            $response2 = $this->createResponseMock($request2),
        ];

        $exceptions = [
            $this->createExceptionMock($request1, $response1),
            $this->createExceptionMock($request2, $response2),
        ];

        $retryResponses = [$this->createResponseMock(), $this->createResponseMock()];

        $this->retry
            ->expects($this->exactly(count($responses)))
            ->method('retry')
            ->will($this->returnValueMap([
                [$request1, false, $retryRequest1 = $this->createRequestMock()],
                [$request2, false, $retryRequest2 = $this->createRequestMock()],
            ]));

        $httpAdapter = $this->createHttpAdapterMock();
        $httpAdapter
            ->expects($this->once())
            ->method('sendRequests')
            ->with($this->identicalTo([$retryRequest1, $retryRequest2]))
            ->will($this->returnValue($retryResponses));

        $this->retrySubscriber->onMultiResponseErrored($event = $this->createMultiRequestErroredEvent($httpAdapter, $exceptions));

        $this->assertFalse($event->hasExceptions());
        $this->assertSame($retryResponses, $event->getResponses());
    }

    public function testMultiRequestErroredEventRetriedThrowException()
    {
        $requests = [
            $request1 = $this->createRequestMock(),
            $request2 = $this->createRequestMock(),
        ];

        $exceptions = [
            $this->createExceptionMock($request1, $response1 = $this->createResponseMock($request1)),
            $this->createExceptionMock($request2, $response2 = $this->createResponseMock($request2)),
        ];

        $this->retry
            ->expects($this->exactly(count($requests)))
            ->method('retry')
            ->will($this->returnValueMap([
                [$request1, false, $retryRequest1 = $this->createRequestMock()],
                [$request2, false, $retryRequest2 = $this->createRequestMock()],
            ]));

        $httpAdapter = $this->createHttpAdapterMock();
        $httpAdapter
            ->expects($this->once())
            ->method('sendRequests')
            ->with($this->identicalTo([$retryRequest1, $retryRequest2]))
            ->will($this->throwException($exception = $this->createMultiExceptionMock($exceptions)));

        $this->retrySubscriber->onMultiResponseErrored($event = $this->createMultiRequestErroredEvent($httpAdapter, $exceptions));

        $this->assertSame($exceptions, $event->getExceptions());
        $this->assertFalse($event->hasResponses());
    }

    public function testMultiRequestErroredEventNotRetried()
    {
        $responses = [
            $this->createResponseMock($request1 = $this->createRequestMock()),
            $this->createResponseMock($request2 = $this->createRequestMock()),
        ];

        $this->retry
            ->expects($this->exactly(count($responses)))
            ->method('retry')
            ->will($this->returnValueMap([
                [$request1, false, false],
                [$request2, false, false],
            ]));

        $httpAdapter = $this->createHttpAdapterMock();
        $httpAdapter
            ->expects($this->never())
            ->method('sendRequests');

        $this->retrySubscriber->onMultiResponseErrored($event = $this->createMultiRequestErroredEvent(
            $httpAdapter,
            [$this->createExceptionMock($request1), $this->createExceptionMock($request2)]
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function createResponseMock(InternalRequestInterface $internalRequest = null)
    {
        $response = parent::createResponseMock();
        $response
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo('request'))
            ->will($this->returnValue($internalRequest));

        return $response;
    }

    /**
     * @return RetryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createRetryMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Event\Retry\RetryInterface');
    }
}
