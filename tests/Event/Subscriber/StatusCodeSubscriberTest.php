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
use Ivory\HttpAdapter\Event\Subscriber\StatusCodeSubscriber;

/**
 * Status code subscriber test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StatusCodeSubscriberTest extends AbstractSubscriberTest
{
    /** @var \Ivory\HttpAdapter\Event\Subscriber\StatusCodeSubscriber */
    protected $statusCodeSubscriber;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->statusCodeSubscriber = new StatusCodeSubscriber();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->statusCodeSubscriber);
    }

    public function testSubscribedEvents()
    {
        $events = StatusCodeSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::POST_SEND, $events);
        $this->assertSame(array('onPostSend', 200), $events[Events::POST_SEND]);
    }

    /**
     * @dataProvider validStatusCodeProvider
     */
    public function testPostSendEventWithValidStatusCode($statusCode)
    {
        $response = $this->createResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue($statusCode));

        $this->statusCodeSubscriber->onPostSend($this->createPostSendEvent(null, null, $response));
    }

    /**
     * @dataProvider invalidStatusCodeProvider
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testPostSendEventWithInvalidStatusCode($statusCode)
    {
        $response = $this->createResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue($statusCode));

        $this->statusCodeSubscriber->onPostSend($this->createPostSendEvent(null, null, $response));
    }

    /**
     * Gets the valid status code provider.
     *
     * @return array The valid status code provider.
     */
    public function validStatusCodeProvider()
    {
        return array(
            array(100),
            array(200),
            array(300),
        );
    }

    /**
     * Gets the invalid status code provider.
     *
     * @return array The invalid status code provider.
     */
    public function invalidStatusCodeProvider()
    {
        return array(
            array(400),
            array(500),
        );
    }
}
