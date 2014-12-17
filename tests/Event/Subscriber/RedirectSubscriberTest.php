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
use Ivory\HttpAdapter\Event\Subscriber\RedirectSubscriber;

/**
 * Redirect subscriber test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class RedirectSubscriberTest extends AbstractSubscriberTest
{
    /** @var \Ivory\HttpAdapter\Event\Subscriber\RedirectSubscriber */
    private $redirectSubscriber;

    /** @var \Ivory\HttpAdapter\Event\Redirect\RedirectInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $redirect;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->redirectSubscriber = new RedirectSubscriber(
            $this->redirect = $this->getMock('Ivory\HttpAdapter\Event\Redirect\RedirectInterface')
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->redirect);
        unset($this->redirectSubscriber);
    }

    public function testDefaultState()
    {
        $this->redirectSubscriber = new RedirectSubscriber();

        $this->assertInstanceOf('Ivory\HttpAdapter\Event\Redirect\Redirect', $this->redirectSubscriber->getRedirect());
    }

    public function testInitialState()
    {
        $this->assertSame($this->redirect, $this->redirectSubscriber->getRedirect());
    }

    public function testSubscribedEvents()
    {
        $events = RedirectSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::POST_SEND, $events);
        $this->assertSame(array('onPostSend', 0), $events[Events::POST_SEND]);
    }

    public function testPostSendEventWithRedirectResponse()
    {
        $httpAdapter = $this->createHttpAdapterMock();
        $httpAdapter
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->identicalTo($redirectRequest = $this->createRequestMock()))
            ->will($this->returnValue($redirectResponse = $this->createResponseMock()));

        $this->redirect
            ->expects($this->once())
            ->method('isRedirectResponse')
            ->with($this->identicalTo($response = $this->createResponseMock()))
            ->will($this->returnValue(true));

        $this->redirect
            ->expects($this->once())
            ->method('isMaxRedirectRequest')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue(false));

        $this->redirect
            ->expects($this->once())
            ->method('createRedirectRequest')
            ->with(
                $this->identicalTo($response),
                $this->identicalTo($request),
                $this->identicalTo($httpAdapter->getConfiguration()->getMessageFactory())
            )
            ->will($this->returnValue($redirectRequest));

        $this->redirectSubscriber->onPostSend($event = $this->createPostSendEvent($httpAdapter, $request, $response));

        $this->assertSame($redirectResponse, $event->getResponse());
    }

    public function testPostSendEventWithoutRedirectResponse()
    {
        $this->redirect
            ->expects($this->once())
            ->method('isRedirectResponse')
            ->with($this->identicalTo($response = $this->createResponseMock()))
            ->will($this->returnValue(false));

        $this->redirect
            ->expects($this->once())
            ->method('prepareResponse')
            ->with($this->identicalTo($response), $this->identicalTo($request = $this->createRequestMock()));

        $this->redirectSubscriber->onPostSend($this->createPostSendEvent(null, $request, $response));
    }

    public function testPostSendEventWithMaxRedirectReachedDontThrowException()
    {
        $this->redirect
            ->expects($this->once())
            ->method('isRedirectResponse')
            ->with($this->identicalTo($response = $this->createResponseMock()))
            ->will($this->returnValue(true));

        $this->redirect
            ->expects($this->once())
            ->method('isMaxRedirectRequest')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue(true));

        $this->redirect
            ->expects($this->once())
            ->method('getThrowException')
            ->will($this->returnValue(false));

        $this->redirect
            ->expects($this->once())
            ->method('prepareResponse')
            ->with($this->identicalTo($response));

        $this->redirectSubscriber->onPostSend($this->createPostSendEvent(null, $request, $response));
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     * @expectedExceptionMessage An error occurred when fetching the URL "url" with the adapter "http_adapter" ("Max redirects exceeded (0)").
     */
    public function testPostSendEventWithMaxRedirectReachedThrowException()
    {
        $this->redirect
            ->expects($this->once())
            ->method('isRedirectResponse')
            ->with($this->identicalTo($response = $this->createResponseMock()))
            ->will($this->returnValue(true));

        $this->redirect
            ->expects($this->once())
            ->method('isMaxRedirectRequest')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue(true));

        $this->redirect
            ->expects($this->once())
            ->method('getThrowException')
            ->will($this->returnValue(true));

        $this->redirect
            ->expects($this->once())
            ->method('getRootRequest')
            ->with($this->identicalTo($request))
            ->will($this->returnValue($rootRequest = $this->createRequestMock()));

        $rootRequest
            ->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue('url'));

        $this->redirectSubscriber->onPostSend($this->createPostSendEvent(null, $request, $response));
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationMock()
    {
        $configuration = parent::createConfigurationMock();
        $configuration
            ->expects($this->any())
            ->method('getMessageFactory')
            ->will($this->returnValue($this->getMock('Ivory\HttpAdapter\Message\MessageFactoryInterface')));

        return $configuration;
    }
}
