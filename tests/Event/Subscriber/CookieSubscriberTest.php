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

use Ivory\HttpAdapter\Event\Cookie\Jar\CookieJarInterface;
use Ivory\HttpAdapter\Event\Events;
use Ivory\HttpAdapter\Event\Subscriber\CookieSubscriber;
use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CookieSubscriberTest extends AbstractSubscriberTest
{
    /**
     * @var CookieSubscriber
     */
    private $cookieSubscriber;

    /**
     * @var CookieJarInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cookieJar;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->cookieSubscriber = new CookieSubscriber($this->cookieJar = $this->createCookieJarMock());
    }

    public function setDefaultState()
    {
        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Cookie\Jar\CookieJar',
            $this->cookieSubscriber->getCookieJar()
        );
    }

    public function testInitialState()
    {
        $this->cookieSubscriber = new CookieSubscriber($cookieJar = $this->createCookieJarMock());

        $this->assertSame($cookieJar, $this->cookieSubscriber->getCookieJar());
    }

    public function testSubscribedEvents()
    {
        $events = CookieSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::REQUEST_CREATED, $events);
        $this->assertSame(['onRequestCreated', 300], $events[Events::REQUEST_CREATED]);

        $this->assertArrayHasKey(Events::REQUEST_SENT, $events);
        $this->assertSame(['onRequestSent', 300], $events[Events::REQUEST_SENT]);

        $this->assertArrayHasKey(Events::REQUEST_ERRORED, $events);
        $this->assertSame(['onRequestErrored', 300], $events[Events::REQUEST_ERRORED]);

        $this->assertArrayHasKey(Events::MULTI_REQUEST_CREATED, $events);
        $this->assertSame(['onMultiRequestCreated', 300], $events[Events::MULTI_REQUEST_CREATED]);

        $this->assertArrayHasKey(Events::MULTI_REQUEST_SENT, $events);
        $this->assertSame(['onMultiRequestSent', 300], $events[Events::MULTI_REQUEST_SENT]);

        $this->assertArrayHasKey(Events::MULTI_REQUEST_ERRORED, $events);
        $this->assertSame(['onMultiResponseErrored', 300], $events[Events::MULTI_REQUEST_ERRORED]);
    }

    public function testRequestCreatedEvent()
    {
        $this->cookieJar
            ->expects($this->once())
            ->method('populate')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue($populatedRequest = $this->createRequestMock()));

        $this->cookieSubscriber->onRequestCreated($event = $this->createRequestCreatedEvent(null, $request));

        $this->assertSame($populatedRequest, $event->getRequest());
    }

    public function testRequestSentEvent()
    {
        $this->cookieJar
            ->expects($this->once())
            ->method('extract')
            ->with(
                $this->identicalTo($request = $this->createRequestMock()),
                $this->identicalTo($response = $this->createResponseMock())
            );

        $this->cookieSubscriber->onRequestSent($this->createRequestSentEvent(null, $request, $response));
    }

    public function testRequestErroredEvent()
    {
        $this->cookieJar
            ->expects($this->once())
            ->method('extract')
            ->with(
                $this->identicalTo($request = $this->createRequestMock()),
                $this->identicalTo($response = $this->createResponseMock())
            );

        $this->cookieSubscriber->onRequestErrored($this->createRequestErroredEvent(
            null,
            $this->createExceptionMock($request, $response)
        ));
    }

    public function testMultiRequestCreatedEvent()
    {
        $requests = [$request1 = $this->createRequestMock(), $request2 = $this->createRequestMock()];

        $this->cookieJar
            ->expects($this->exactly(count($requests)))
            ->method('populate')
            ->will($this->returnValueMap([
                [$request1, $populatedRequest1 = $this->createRequestMock()],
                [$request2, $populatedRequest2 = $this->createRequestMock()],
            ]));

        $this->cookieSubscriber->onMultiRequestCreated($event = $this->createMultiRequestCreatedEvent(null, $requests));

        $this->assertSame([$populatedRequest1, $populatedRequest2], $event->getRequests());
    }

    public function testMultiRequestSentEvent()
    {
        $request1 = $this->createRequestMock();
        $request2 = $this->createRequestMock();

        $responses = [
            $response1 = $this->createResponseMock($request1),
            $response2 = $this->createResponseMock($request2),
        ];

        $this->cookieJar
            ->expects($this->exactly(count($responses)))
            ->method('extract')
            ->withConsecutive([$request1, $response1], [$request2, $response2]);

        $this->cookieSubscriber->onMultiRequestSent($this->createMultiRequestSentEvent(null, $responses));
    }

    public function testMultiRequestErroredEvent()
    {
        $exceptions = [
            $this->createExceptionMock(
                $request1 = $this->createRequestMock(),
                $response1 = $this->createResponseMock($request1)
            ),
            $this->createExceptionMock(
                $request2 = $this->createRequestMock(),
                $response2 = $this->createResponseMock($request2)
            ),
        ];

        $this->cookieJar
            ->expects($this->exactly(count($exceptions)))
            ->method('extract')
            ->withConsecutive(
                [$request1, $response1],
                [$request2, $response2]
            );

        $this->cookieSubscriber->onMultiResponseErrored($this->createMultiRequestErroredEvent(null, $exceptions));
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
     * @return CookieJarInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createCookieJarMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Event\Cookie\Jar\CookieJarInterface');
    }
}
