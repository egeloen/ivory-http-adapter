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

use Ivory\HttpAdapter\Event\Cache\CacheInterface;
use Ivory\HttpAdapter\Event\Events;
use Ivory\HttpAdapter\Event\Subscriber\CacheSubscriber;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CacheSubscriberTest extends AbstractSubscriberTest
{
    /**
     * @var CacheSubscriber
     */
    private $cacheSubscriber;

    /**
     * @var CacheInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cache;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->cache = $this->createCacheMock();
        $this->cacheSubscriber = new CacheSubscriber($this->cache);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->cache, $this->cacheSubscriber->getCache());
    }

    public function testSubscribedEvents()
    {
        $events = CacheSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::REQUEST_CREATED, $events);
        $this->assertSame(['onRequestCreated', -100], $events[Events::REQUEST_CREATED]);

        $this->assertArrayHasKey(Events::REQUEST_SENT, $events);
        $this->assertSame(['onRequestSent', -100], $events[Events::REQUEST_SENT]);

        $this->assertArrayHasKey(Events::REQUEST_ERRORED, $events);
        $this->assertSame(['onRequestErrored', -100], $events[Events::REQUEST_ERRORED]);

        $this->assertArrayHasKey(Events::MULTI_REQUEST_CREATED, $events);
        $this->assertSame(['onMultiRequestCreated', -100], $events[Events::MULTI_REQUEST_CREATED]);

        $this->assertArrayHasKey(Events::MULTI_REQUEST_SENT, $events);
        $this->assertSame(['onMultiRequestSent', -100], $events[Events::MULTI_REQUEST_SENT]);

        $this->assertArrayHasKey(Events::MULTI_REQUEST_ERRORED, $events);
        $this->assertSame(['onMultiRequestErrored', -100], $events[Events::MULTI_REQUEST_ERRORED]);
    }

    public function testRequestCreatedEvent()
    {
        $messageFactory = $this->createMessageFactoryMock();
        $configuration = $this->createConfigurationMock($messageFactory);
        $httpAdapter = $this->createHttpAdapterMock($configuration);
        $request = $this->createRequestMock();

        $this->cache
            ->expects($this->once())
            ->method('getResponse')
            ->with(
                $this->identicalTo($request),
                $this->identicalTo($messageFactory)
            );

        $this->cache
            ->expects($this->once())
            ->method('getException')
            ->with(
                $this->identicalTo($request),
                $this->identicalTo($messageFactory)
            );

        $this->cacheSubscriber->onRequestCreated($event = $this->createRequestCreatedEvent($httpAdapter, $request));

        $this->assertNull($event->getResponse());
        $this->assertNull($event->getException());
    }

    public function testRequestCreatedEventWithCachedResponse()
    {
        $messageFactory = $this->createMessageFactoryMock();
        $configuration = $this->createConfigurationMock($messageFactory);
        $httpAdapter = $this->createHttpAdapterMock($configuration);
        $request = $this->createRequestMock();

        $this->cache
            ->expects($this->once())
            ->method('getResponse')
            ->with(
                $this->identicalTo($request),
                $this->identicalTo($messageFactory)
            )
            ->will($this->returnValue($response = $this->createResponseMock()));

        $this->cacheSubscriber->onRequestCreated($event = $this->createRequestCreatedEvent($httpAdapter, $request));

        $this->assertSame($response, $event->getResponse());
        $this->assertNull($event->getException());
    }

    public function testRequestCreatedEventWithCachedException()
    {
        $messageFactory = $this->createMessageFactoryMock();
        $configuration = $this->createConfigurationMock($messageFactory);
        $httpAdapter = $this->createHttpAdapterMock($configuration);
        $request = $this->createRequestMock();

        $this->cache
            ->expects($this->once())
            ->method('getResponse')
            ->with(
                $this->identicalTo($request),
                $this->identicalTo($messageFactory)
            );

        $this->cache
            ->expects($this->once())
            ->method('getException')
            ->with(
                $this->identicalTo($request),
                $this->identicalTo($messageFactory)
            )
            ->will($this->returnValue($exception = $this->createExceptionMock()));

        $this->cacheSubscriber->onRequestCreated($event = $this->createRequestCreatedEvent($httpAdapter, $request));

        $this->assertNull($event->getResponse());
        $this->assertSame($exception, $event->getException());
    }

    public function testRequestSent()
    {
        $this->cache
            ->expects($this->once())
            ->method('saveResponse')
            ->with(
                $this->identicalTo($response = $this->createResponseMock()),
                $this->identicalTo($request = $this->createRequestMock())
            );

        $this->cacheSubscriber->onRequestSent($event = $this->createRequestSentEvent(null, $request, $response));
    }

    public function testRequestErrored()
    {
        $this->cache
            ->expects($this->once())
            ->method('saveException')
            ->with(
                $this->identicalTo($exception = $this->createExceptionMock($request = $this->createRequestMock())),
                $this->identicalTo($request)
            );

        $this->cacheSubscriber->onRequestErrored($event = $this->createRequestErroredEvent(null, $exception));
    }

    public function testMultiRequestCreatedEvent()
    {
        $messageFactory = $this->createMessageFactoryMock();
        $configuration = $this->createConfigurationMock($messageFactory);
        $httpAdapter = $this->createHttpAdapterMock($configuration);
        $requests = [$request1 = $this->createRequestMock(), $request2 = $this->createRequestMock()];

        $this->cache
            ->expects($this->exactly(2))
            ->method('getResponse')
            ->withConsecutive(
                [$request1, $messageFactory],
                [$request2, $messageFactory]
            );

        $this->cache
            ->expects($this->exactly(2))
            ->method('getException')
            ->withConsecutive(
                [$request1, $messageFactory],
                [$request2, $messageFactory]
            );

        $this->cacheSubscriber->onMultiRequestCreated(
            $event = $this->createMultiRequestCreatedEvent($httpAdapter, $requests)
        );

        $this->assertSame($requests, $event->getRequests());
        $this->assertEmpty($event->getResponses());
        $this->assertEmpty($event->getExceptions());
    }

    public function testMultiRequestCreatedEventCachedResponses()
    {
        $messageFactory = $this->createMessageFactoryMock();
        $configuration = $this->createConfigurationMock($messageFactory);
        $httpAdapter = $this->createHttpAdapterMock($configuration);
        $requests = [$request1 = $this->createRequestMock(), $request2 = $this->createRequestMock()];

        $this->cache
            ->expects($this->exactly(2))
            ->method('getResponse')
            ->will($this->returnValueMap([
                [$request1, $messageFactory, $response1 = $this->createResponseMock()],
                [$request2, $messageFactory, $response2 = $this->createResponseMock()],
            ]));

        $this->cacheSubscriber->onMultiRequestCreated(
            $event = $this->createMultiRequestCreatedEvent($httpAdapter, $requests)
        );

        $this->assertEmpty($event->getRequests());
        $this->assertSame([$response1, $response2], $event->getResponses());
        $this->assertEmpty($event->getExceptions());
    }

    public function testMultiRequestCreatedEventCachedExceptions()
    {
        $messageFactory = $this->createMessageFactoryMock();
        $configuration = $this->createConfigurationMock($messageFactory);
        $httpAdapter = $this->createHttpAdapterMock($configuration);
        $requests = [$request1 = $this->createRequestMock(), $request2 = $this->createRequestMock()];

        $this->cache
            ->expects($this->exactly(2))
            ->method('getResponse')
            ->withConsecutive(
                [$request1, $messageFactory],
                [$request2, $messageFactory]
            );

        $this->cache
            ->expects($this->exactly(2))
            ->method('getException')
            ->will($this->returnValueMap([
                [$request1, $messageFactory, $exception1 = $this->createExceptionMock($request1)],
                [$request2, $messageFactory, $exception2 = $this->createExceptionMock($request2)],
            ]));

        $this->cacheSubscriber->onMultiRequestCreated(
            $event = $this->createMultiRequestCreatedEvent($httpAdapter, $requests)
        );

        $this->assertEmpty($event->getRequests());
        $this->assertEmpty($event->getResponses());
        $this->assertSame([$exception1, $exception2], $event->getExceptions());
    }

    public function testMultiRequestSent()
    {
        $this->cache
            ->expects($this->exactly(2))
            ->method('saveResponse')
            ->withConsecutive(
                [$response1 = $this->createResponseMock(), $request1 = $this->createRequestMock()],
                [$response2 = $this->createResponseMock(), $request2 = $this->createRequestMock()]
            );

        $response1
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo('request'))
            ->will($this->returnValue($request1));

        $response2
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo('request'))
            ->will($this->returnValue($request2));

        $this->cacheSubscriber->onMultiRequestSent($this->createMultiRequestSentEvent(null, [$response1, $response2]));
    }

    public function testMultiRequestErrored()
    {
        $this->cache
            ->expects($this->exactly(2))
            ->method('saveException')
            ->withConsecutive(
                [$exception1 = $this->createExceptionMock($request1 = $this->createRequestMock()), $request1],
                [$exception2 = $this->createExceptionMock($request2 = $this->createRequestMock()), $request2]
            );

        $this->cache
            ->expects($this->exactly(2))
            ->method('saveResponse')
            ->withConsecutive(
                [$response1 = $this->createResponseMock(), $request3 = $this->createRequestMock()],
                [$response2 = $this->createResponseMock(), $request4 = $this->createRequestMock()]
            );

        $response1
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo('request'))
            ->will($this->returnValue($request3));

        $response2
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo('request'))
            ->will($this->returnValue($request4));

        $this->cacheSubscriber->onMultiRequestErrored(
            $this->createMultiRequestErroredEvent(null, [$exception1, $exception2], [$response1, $response2])
        );
    }

    /**
     * @return CacheInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createCacheMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Event\Cache\CacheInterface');
    }
}
