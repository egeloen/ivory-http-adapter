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
use Ivory\HttpAdapter\Event\Subscriber\CookieSubscriber;
use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * Cookie subscriber test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CookieSubscriberTest extends AbstractSubscriberTest
{
    /** @var \Ivory\HttpAdapter\Event\Subscriber\CookieSubscriber */
    private $cookieSubscriber;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->cookieSubscriber = new CookieSubscriber();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->cookieSubscriber);
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

    public function testSetCookieJar()
    {
        $this->cookieSubscriber->setCookieJar($cookieJar = $this->createCookieJarMock());

        $this->assertSame($cookieJar, $this->cookieSubscriber->getCookieJar());
    }

    public function testSubscribedEvents()
    {
        $events = CookieSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::PRE_SEND, $events);
        $this->assertSame(array('onPreSend', 300), $events[Events::PRE_SEND]);

        $this->assertArrayHasKey(Events::POST_SEND, $events);
        $this->assertSame(array('onPostSend', 300), $events[Events::POST_SEND]);

        $this->assertArrayHasKey(Events::EXCEPTION, $events);
        $this->assertSame(array('onException', 300), $events[Events::EXCEPTION]);

        $this->assertArrayHasKey(Events::MULTI_PRE_SEND, $events);
        $this->assertSame(array('onMultiPreSend', 300), $events[Events::MULTI_PRE_SEND]);

        $this->assertArrayHasKey(Events::MULTI_POST_SEND, $events);
        $this->assertSame(array('onMultiPostSend', 300), $events[Events::MULTI_POST_SEND]);

        $this->assertArrayHasKey(Events::MULTI_EXCEPTION, $events);
        $this->assertSame(array('onMultiException', 300), $events[Events::MULTI_EXCEPTION]);
    }

    public function testPreSendEvent()
    {
        $this->cookieSubscriber->setCookieJar($cookieJar = $this->createCookieJarMock());

        $cookieJar
            ->expects($this->once())
            ->method('populate')
            ->with($this->identicalTo($request = $this->createRequestMock()));

        $this->cookieSubscriber->onPreSend($this->createPreSendEvent(null, $request));
    }

    public function testPostSendEvent()
    {
        $this->cookieSubscriber->setCookieJar($cookieJar = $this->createCookieJarMock());

        $cookieJar
            ->expects($this->once())
            ->method('extract')
            ->with(
                $this->identicalTo($request = $this->createRequestMock()),
                $this->identicalTo($response = $this->createResponseMock())
            );

        $this->cookieSubscriber->onPostSend($this->createPostSendEvent(null, $request, $response));
    }

    public function testExceptionEvent()
    {
        $this->cookieSubscriber->setCookieJar($cookieJar = $this->createCookieJarMock());

        $cookieJar
            ->expects($this->once())
            ->method('extract')
            ->with(
                $this->identicalTo($request = $this->createRequestMock()),
                $this->identicalTo($response = $this->createResponseMock())
            );

        $this->cookieSubscriber->onException($this->createExceptionEvent(
            null,
            $this->createExceptionMock($request, $response)
        ));
    }

    public function testMultiPreSendEvent()
    {
        $this->cookieSubscriber->setCookieJar($cookieJar = $this->createCookieJarMock());

        $requests = array($request1 = $this->createRequestMock(), $request2 = $this->createRequestMock());

        $cookieJar
            ->expects($this->exactly(count($requests)))
            ->method('populate')
            ->withConsecutive(array($request1), array($request2));

        $this->cookieSubscriber->onMultiPreSend($this->createMultiPreSendEvent(null, $requests));
    }

    public function testMultiPostSendEvent()
    {
        $this->cookieSubscriber->setCookieJar($cookieJar = $this->createCookieJarMock());

        $request1 = $this->createRequestMock();
        $request2 = $this->createRequestMock();

        $responses = array(
            $response1 = $this->createResponseMock($request1),
            $response2 = $this->createResponseMock($request2),
        );

        $cookieJar
            ->expects($this->exactly(count($responses)))
            ->method('extract')
            ->withConsecutive(array($request1, $response1), array($request2, $response2));

        $this->cookieSubscriber->onMultiPostSend($this->createMultiPostSendEvent(null, $responses));
    }

    public function testMultiExceptionEvent()
    {
        $this->cookieSubscriber->setCookieJar($cookieJar = $this->createCookieJarMock());

        $exceptions = array(
            $this->createExceptionMock(
                $request1 = $this->createRequestMock(),
                $response1 = $this->createResponseMock($request1)
            ),
            $this->createExceptionMock(
                $request2 = $this->createRequestMock(),
                $response2 = $this->createResponseMock($request2)
            ),
        );

        $cookieJar
            ->expects($this->exactly(count($exceptions)))
            ->method('extract')
            ->withConsecutive(
                array($request1, $response1),
                array($request2, $response2)
            );

        $this->cookieSubscriber->onMultiException($this->createMultiExceptionEvent(null, $exceptions));
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
     * Creates a cookie jar mock.
     *
     * @return \Ivory\HttpAdapter\Event\Cookie\Jar\CookieJarInterface|\PHPUnit_Framework_MockObject_MockObject The cookie jar mock.
     */
    private function createCookieJarMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Event\Cookie\Jar\CookieJarInterface');
    }
}
