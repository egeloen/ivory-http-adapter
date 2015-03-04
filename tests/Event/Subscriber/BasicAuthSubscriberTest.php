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

    public function testSetBasicAuth()
    {
        $this->basicAuthSubscriber->setBasicAuth($basicAuth = $this->createBasicAuthMock());

        $this->assertSame($basicAuth, $this->basicAuthSubscriber->getBasicAuth());
    }

    public function testSubscribedEvents()
    {
        $events = BasicAuthSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::PRE_SEND, $events);
        $this->assertSame(array('onPreSend', 300), $events[Events::PRE_SEND]);

        $this->assertArrayHasKey(Events::MULTI_PRE_SEND, $events);
        $this->assertSame(array('onMultiPreSend', 300), $events[Events::MULTI_PRE_SEND]);
    }

    public function testPreSendEvent()
    {
        $this->basicAuth
            ->expects($this->once())
            ->method('authenticate')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue($authenticatedRequest = $this->createRequestMock()));

        $this->basicAuthSubscriber->onPreSend($event = $this->createPreSendEvent(null, $request));

        $this->assertSame($authenticatedRequest, $event->getRequest());
    }

    public function testMultiPreSendEvent()
    {
        $requests = array($request1 = $this->createRequestMock(), $request2 = $this->createRequestMock());

        $this->basicAuth
            ->expects($this->exactly(count($requests)))
            ->method('authenticate')
            ->will($this->returnValueMap(array(
                array($request1, $authenticatedRequest1 = $this->createRequestMock()),
                array($request2, $authenticatedRequest2 = $this->createRequestMock()),
            )));

        $this->basicAuthSubscriber->onMultiPreSend($event = $this->createMultiPreSendEvent(null, $requests));

        $this->assertSame(array($authenticatedRequest1, $authenticatedRequest2), $event->getRequests());
    }

    /**
     * Creates a basic auth mock.
     *
     * @return \Ivory\HttpAdapter\Event\BasicAuth\BasicAuthInterface|\PHPUnit_Framework_MockObject_MockObject The basic auth mock.
     */
    private function createBasicAuthMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Event\BasicAuth\BasicAuthInterface');
    }
}
