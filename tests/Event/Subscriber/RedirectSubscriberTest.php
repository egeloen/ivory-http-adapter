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

    public function testPostSendEvent()
    {
        $this->redirect
            ->expects($this->once())
            ->method('redirect')
            ->with(
                $this->identicalTo($response = $this->createResponseMock()),
                $this->identicalTo($request = $this->createRequestMock()),
                $httpAdapter = $this->createHttpAdapterMock()
            )
            ->will($this->returnValue($redirectResponse = $this->createResponseMock()));

        $this->redirectSubscriber->onPostSend($event = $this->createPostSendEvent($httpAdapter, $request, $response));

        $this->assertSame($redirectResponse, $event->getResponse());
    }
}
