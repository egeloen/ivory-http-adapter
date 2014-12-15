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
    private $statusCodeSubscriber;

    /** @var \Ivory\HttpAdapter\Event\StatusCode\StatusCodeInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $statusCode;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->statusCodeSubscriber = new StatusCodeSubscriber($this->statusCode = $this->createStatusCodeMock());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->statusCode);
        unset($this->statusCodeSubscriber);
    }

    public function testDefaultState()
    {
        $this->statusCodeSubscriber = new StatusCodeSubscriber();

        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\StatusCode\StatusCode',
            $this->statusCodeSubscriber->getStatusCode()
        );
    }

    public function testInitialState()
    {
        $this->assertSame($this->statusCode, $this->statusCodeSubscriber->getStatusCode());
    }

    public function testSubscribedEvents()
    {
        $events = StatusCodeSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::POST_SEND, $events);
        $this->assertSame(array('onPostSend', 200), $events[Events::POST_SEND]);
    }

    public function testPostSendEventWithValidStatusCode()
    {
        $this->statusCode
            ->expects($this->once())
            ->method('validate')
            ->with($this->identicalTo($response = $this->createResponseMock($valid = true)))
            ->will($this->returnValue($valid));

        $this->statusCodeSubscriber->onPostSend($this->createPostSendEvent(null, null, $response));
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     * @expectedExceptionMessage An error occurred when fetching the URL "http://egeloen.fr" with the adapter "name" ("Status code: 500").
     */
    public function testPostSendEventWithInvalidStatusCode()
    {
        $this->statusCode
            ->expects($this->once())
            ->method('validate')
            ->with($this->identicalTo($response = $this->createResponseMock($valid = false)))
            ->will($this->returnValue($valid));

        $this->statusCodeSubscriber->onPostSend($this->createPostSendEvent(null, null, $response));
    }

    /**
     * {@inheritdoc}
     */
    protected function createHttpAdapterMock()
    {
        $httpAdapter = parent::createHttpAdapterMock();
        $httpAdapter
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('name'));

        return $httpAdapter;
    }

    /**
     * {@inheritdoc}
     */
    protected function createRequestMock()
    {
        $request = parent::createRequestMock();
        $request
            ->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue('http://egeloen.fr'));

        return $request;
    }

    /**
     * Creates a response mock.
     *
     * @param boolean $valid TRUE if the status code is valid else FALSE.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject The response mock.
     */
    protected function createResponseMock($valid = true)
    {
        $response = parent::createResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue($valid ? 200 : 500));

        return $response;
    }

    /**
     * Creates a status code mock.
     *
     * @return \Ivory\HttpAdapter\Event\StatusCode\StatusCodeInterface|\PHPUnit_Framework_MockObject_MockObject The status code mock.
     */
    private function createStatusCodeMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Event\StatusCode\StatusCodeInterface');
    }
}
