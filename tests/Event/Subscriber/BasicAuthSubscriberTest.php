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
use Ivory\HttpAdapter\Event\Subscriber\BasicAuthSubscriber;

/**
 * Basic auth subscriber test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class BasicAuthSubscriberTest extends AbstractSubscriberTest
{
    /** @var \Ivory\HttpAdapter\Event\Subscriber\BasicAuthSubscriber */
    private $basicAuthSubscriber;

    /** @var \Ivory\HttpAdapter\Event\BasicAuth\BasicAuthInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $basicAuth;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->basicAuthSubscriber = new BasicAuthSubscriber($this->basicAuth = $this->createBasicAuthMock());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->basicAuth);
        unset($this->basicAuthSubscriber);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->basicAuth, $this->basicAuthSubscriber->getBasicAuth());
    }

    public function testSubscribedEvents()
    {
        $events = BasicAuthSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::REQUEST_CREATED, $events);
        $this->assertSame(array('onRequestCreated', 300), $events[Events::REQUEST_CREATED]);

        $this->assertArrayHasKey(Events::MULTI_REQUEST_CREATED, $events);
        $this->assertSame(array('onMultiRequestCreated', 300), $events[Events::MULTI_REQUEST_CREATED]);
    }

    public function testRequestCreatedEvent()
    {
        $this->basicAuth
            ->expects($this->once())
            ->method('authenticate')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue($authenticatedRequest = $this->createRequestMock()));

        $this->basicAuthSubscriber->onRequestCreated($event = $this->createRequestCreatedEvent(null, $request));

        $this->assertSame($authenticatedRequest, $event->getRequest());
    }

    public function testMultiRequestCreatedEvent()
    {
        $requests = array($request1 = $this->createRequestMock(), $request2 = $this->createRequestMock());

        $this->basicAuth
            ->expects($this->exactly(count($requests)))
            ->method('authenticate')
            ->will($this->returnValueMap(array(
                array($request1, $authenticatedRequest1 = $this->createRequestMock()),
                array($request2, $authenticatedRequest2 = $this->createRequestMock()),
            )));

        $this->basicAuthSubscriber->onMultiRequestCreated($event = $this->createMultiRequestCreatedEvent(null, $requests));

        $this->assertSame(array($authenticatedRequest1, $authenticatedRequest2), $event->getRequests());
    }

    /**
     * Creates a basic auth mock.
     *
     * @return \Ivory\HttpAdapter\Event\BasicAuth\BasicAuthInterface|\PHPUnit_Framework_MockObject_MockObject The basic auth mock.
     */
    private function createBasicAuthMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Event\BasicAuth\BasicAuthInterface');
    }
}
