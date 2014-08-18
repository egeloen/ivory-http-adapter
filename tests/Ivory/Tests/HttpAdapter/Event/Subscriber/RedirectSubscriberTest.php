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
use Ivory\HttpAdapter\HttpAdapterException;

/**
 * Redirect subscriber test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class RedirectSubscriberTest extends AbstractSubscriberTest
{
    /** @var \Ivory\HttpAdapter\Event\Subscriber\RedirectSubscriber */
    protected $redirectSubscriber;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->redirectSubscriber = new RedirectSubscriber();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->redirectSubscriber);
    }

    public function testDefaultState()
    {
        $this->assertSame(5, $this->redirectSubscriber->getMaxRedirects());
    }

    public function testInitialState()
    {
        $this->redirectSubscriber = new RedirectSubscriber($maxRedirects = 10);

        $this->assertSame($maxRedirects, $this->redirectSubscriber->getMaxRedirects());
    }

    public function testSetMaxRedirects()
    {
        $this->redirectSubscriber->setMaxRedirects($maxRedirects = 10);

        $this->assertSame($maxRedirects, $this->redirectSubscriber->getMaxRedirects());
    }

    public function testSubscribedEvents()
    {
        $events = RedirectSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::POST_SEND, $events);
        $this->assertSame('onPostSend', $events[Events::POST_SEND]);
    }

    public function testPostSendEventWithoutRedirect()
    {
        $request = $this->createRequestMock();
        $request
            ->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue($url = 'http://egeloen.fr'));

        $response = $this->createResponseMock();
        $response
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                array($this->identicalTo(RedirectSubscriber::REDIRECT_COUNT), $this->identicalTo(0)),
                array($this->identicalTo(RedirectSubscriber::EFFECTIVE_URL), $this->identicalTo($url))
            );

        $this->redirectSubscriber->onPostSend($postSendEvent = $this->createPostSendEvent(null, $request, $response));

        $this->assertSame($request, $postSendEvent->getRequest());
        $this->assertSame($response, $postSendEvent->getResponse());
    }

    public function testPostSendEventWithRedirect()
    {
        $request = $this->createRequestMock();
        $request
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo(RedirectSubscriber::REDIRECT_COUNT))
            ->will($this->returnValue(null));

        $response = $this->createResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(300));

        $response
            ->expects($this->any())
            ->method('hasHeader')
            ->with($this->identicalTo('Location'))
            ->will($this->returnValue(true));

        $response
            ->expects($this->any())
            ->method('getHeader')
            ->with($this->identicalTo('Location'))
            ->will($this->returnValue($location = 'http://egeloen.fr'));

        $messageFactory = $this->createMessageFactoryMock();
        $messageFactory
            ->expects($this->once())
            ->method('cloneInternalRequest')
            ->with($this->identicalTo($request))
            ->will($this->returnValue($requestClone = $this->createRequestMock()));

        $httpAdapter = $this->createHttpAdapterMock();
        $httpAdapter
            ->expects($this->once())
            ->method('getMessageFactory')
            ->will($this->returnValue($messageFactory));

        $requestClone
            ->expects($this->once())
            ->method('setUrl')
            ->with($this->identicalTo($location));

        $requestClone
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                array($this->identicalTo(RedirectSubscriber::PARENT_REQUEST), $this->identicalTo($request)),
                array($this->identicalTo(RedirectSubscriber::REDIRECT_COUNT), $this->identicalTo(1))
            );

        $httpAdapter
            ->expects($this->once())
            ->method('sendInternalRequest')
            ->with($this->identicalTo($requestClone))
            ->will($this->returnValue($redirectResponse = $this->createResponseMock()));

        $postSendEvent = $this->createPostSendEvent($httpAdapter, $request, $response);
        $this->redirectSubscriber->onPostSend($postSendEvent);

        $this->assertSame($request, $postSendEvent->getRequest());
        $this->assertSame($redirectResponse, $postSendEvent->getResponse());
    }

    public function testPostSendEventWithMaxRedirectsExceeded()
    {
        $this->redirectSubscriber->setMaxRedirects($maxRedirects = 1);

        $request = $this->createRequestMock();
        $request
            ->expects($this->any())
            ->method('hasParameter')
            ->with($this->identicalTo(RedirectSubscriber::PARENT_REQUEST))
            ->will($this->returnValue(true));

        $request
            ->expects($this->any())
            ->method('getParameter')
            ->will($this->returnValueMap(array(
                array(RedirectSubscriber::REDIRECT_COUNT, 1),
                array(RedirectSubscriber::PARENT_REQUEST, $parentRequest = $this->createRequestMock())
            )));

        $parentRequest
            ->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue($url = 'http://egeloen.fr'));

        $response = $this->createResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(300));

        $response
            ->expects($this->any())
            ->method('hasHeader')
            ->with($this->identicalTo('Location'))
            ->will($this->returnValue(true));

        $httpAdapter = $this->createHttpAdapterMock();
        $httpAdapter
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($httpAdapterName = 'name'));

        $postSendEvent = $this->createPostSendEvent($httpAdapter, $request, $response);

        try {
            $this->redirectSubscriber->onPostSend($postSendEvent);
            $this->fail();
        } catch (HttpAdapterException $e) {
            $this->assertContains($url, $e->getMessage());
            $this->assertContains((string) $maxRedirects, $e->getMessage());
            $this->assertContains($httpAdapterName, $e->getMessage());
        }
    }

    /**
     * Creates a message factory mock.
     *
     * @return \Ivory\HttpAdapter\Message\MessageFactoryInterface|\PHPUnit_Framework_MockObject_MockObject The message factory mock.
     */
    protected function createMessageFactoryMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\MessageFactoryInterface');
    }
}
