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
use Ivory\HttpAdapter\Message\RequestInterface;
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
        $this->assertSame(5, $this->redirectSubscriber->getMax());
        $this->assertFalse($this->redirectSubscriber->isStrict());
        $this->assertTrue($this->redirectSubscriber->getThrowException());
    }

    public function testInitialState()
    {
        $this->redirectSubscriber = new RedirectSubscriber($max = 10, true, false);

        $this->assertSame($max, $this->redirectSubscriber->getMax());
        $this->assertTrue($this->redirectSubscriber->isStrict());
        $this->assertFalse($this->redirectSubscriber->getThrowException());
    }

    public function testSetMax()
    {
        $this->redirectSubscriber->setMax($max = 10);

        $this->assertSame($max, $this->redirectSubscriber->getMax());
    }

    public function testStrict()
    {
        $this->redirectSubscriber->setStrict(true);

        $this->assertTrue($this->redirectSubscriber->isStrict());
    }

    public function testSetThrowException()
    {
        $this->redirectSubscriber->setThrowException(false);

        $this->assertFalse($this->redirectSubscriber->getThrowException());
    }

    public function testSubscribedEvents()
    {
        $events = RedirectSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::POST_SEND, $events);
        $this->assertSame(array('onPostSend', 0), $events[Events::POST_SEND]);
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

    /**
     * @dataProvider strictStatusCodeProvider
     */
    public function testPostSendEventWithStrictRedirect($statusCode)
    {
        $this->redirectSubscriber->setStrict(true);

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
            ->will($this->returnValue($statusCode));

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
            ->getConfiguration()
            ->expects($this->once())
            ->method('getMessageFactory')
            ->will($this->returnValue($messageFactory));

        $requestClone
            ->expects($this->never())
            ->method('setMethod');

        $requestClone
            ->expects($this->never())
            ->method('removeHeaders');

        $requestClone
            ->expects($this->never())
            ->method('clearRawDatas');

        $requestClone
            ->expects($this->never())
            ->method('clearDatas');

        $requestClone
            ->expects($this->never())
            ->method('clearFiles');

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

    /**
     * @dataProvider statusCodeProvider
     */
    public function testPostSendEventWithNoStrictRedirect($statusCode)
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
            ->will($this->returnValue($statusCode));

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
            ->getConfiguration()
            ->expects($this->once())
            ->method('getMessageFactory')
            ->will($this->returnValue($messageFactory));

        $requestClone
            ->expects($this->once())
            ->method('setMethod')
            ->with($this->identicalTo(RequestInterface::METHOD_GET));

        $requestClone
            ->expects($this->once())
            ->method('removeHeaders')
            ->with($this->identicalTo(array('Content-Type', 'Content-Length')));

        $requestClone
            ->expects($this->once())
            ->method('clearRawDatas');

        $requestClone
            ->expects($this->once())
            ->method('clearDatas');

        $requestClone
            ->expects($this->once())
            ->method('clearFiles');

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

    public function testPostSendEventWithNoStrictRedirectAnd303ResponseButNotStrictly()
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
            ->will($this->returnValue(303));

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
            ->getConfiguration()
            ->expects($this->once())
            ->method('getMessageFactory')
            ->will($this->returnValue($messageFactory));

        $requestClone
            ->expects($this->once())
            ->method('setMethod')
            ->with($this->identicalTo(RequestInterface::METHOD_GET));

        $requestClone
            ->expects($this->once())
            ->method('removeHeaders')
            ->with($this->identicalTo(array('Content-Type', 'Content-Length')));

        $requestClone
            ->expects($this->once())
            ->method('clearRawDatas');

        $requestClone
            ->expects($this->once())
            ->method('clearDatas');

        $requestClone
            ->expects($this->once())
            ->method('clearFiles');

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

    public function testPostSendEventWithMaxRedirectsExceededAndThrowException()
    {
        $this->redirectSubscriber->setMax($max = 1);

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
            $this->assertContains((string) $max, $e->getMessage());
            $this->assertContains($httpAdapterName, $e->getMessage());
        }
    }

    public function testPostSendEventWithMaxRedirectsExceededButWithoutThrowException()
    {
        $this->redirectSubscriber->setMax(1);
        $this->redirectSubscriber->setThrowException(false);

        $request = $this->createRequestMock();
        $request
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo(RedirectSubscriber::REDIRECT_COUNT))
            ->will($this->returnValue($redirectCount = 1));

        $request
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

        $response
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                array($this->identicalTo(RedirectSubscriber::REDIRECT_COUNT), $this->identicalTo($redirectCount)),
                array($this->identicalTo(RedirectSubscriber::EFFECTIVE_URL), $this->identicalTo($url))
            );

        $postSendEvent = $this->createPostSendEvent(null, $request, $response);
        $this->redirectSubscriber->onPostSend($postSendEvent);
    }

    /**
     * Gets the status code provider.
     *
     * @return array The status code provider.
     */
    public function statusCodeProvider()
    {
        return array_merge(
            $this->strictStatusCodeProvider(),
            array(array(303))
        );
    }

    /**
     * Gets the strict status code provider.
     *
     * @return array The strict status code provider.
     */
    public function strictStatusCodeProvider()
    {
        return array(
            array(300),
            array(301),
            array(302),
        );
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
