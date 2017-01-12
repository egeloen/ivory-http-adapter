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
use Ivory\HttpAdapter\Message\InternalRequestInterface;

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

        $this->assertArrayHasKey(Events::REQUEST_SENT, $events);
        $this->assertSame(array('onRequestSent', 200), $events[Events::REQUEST_SENT]);

        $this->assertArrayHasKey(Events::MULTI_REQUEST_SENT, $events);
        $this->assertSame(array('onMultiRequestSent', 200), $events[Events::MULTI_REQUEST_SENT]);
    }

    public function testRequestSentEventWithValidStatusCode()
    {
        $this->statusCode
            ->expects($this->once())
            ->method('validate')
            ->with($this->identicalTo($response = $this->createResponseMock(null, $valid = true)))
            ->will($this->returnValue($valid));

        $this->statusCodeSubscriber->onRequestSent($event = $this->createRequestSentEvent(null, null, $response));

        $this->assertFalse($event->hasException());
    }

    public function testRequestSentEventWithInvalidStatusCode()
    {
        $this->statusCode
            ->expects($this->once())
            ->method('validate')
            ->with($this->identicalTo($response = $this->createResponseMock(null, $valid = false)))
            ->will($this->returnValue($valid));

        $this->statusCodeSubscriber->onRequestSent($event = $this->createRequestSentEvent(null, null, $response));

        $this->assertTrue($event->hasException());
        $this->assertSame(
            'An error occurred when fetching the URI "http://egeloen.fr" with the adapter "http_adapter" ("Status code: 500").',
            $event->getException()->getMessage()
        );
    }

    public function testMultiRequestSentEventWithValidStatusCode()
    {
        $responses = array($response1 = $this->createResponseMock(), $response2 = $this->createResponseMock());

        $this->statusCode
            ->expects($this->exactly(count($responses)))
            ->method('validate')
            ->will($this->returnValueMap(array(
                array($response1, true),
                array($response2, true),
            )));

        $this->statusCodeSubscriber->onMultiRequestSent($event = $this->createMultiRequestSentEvent(null, $responses));

        $this->assertFalse($event->hasExceptions());
        $this->assertTrue($event->hasResponses());
        $this->assertSame($responses, $event->getResponses());
    }

    public function testMultiRequestSentEventWithInvalidStatusCode()
    {
        $responses = array(
            $response1 = $this->createResponseMock($this->createRequestMock(), false),
            $response2 = $this->createResponseMock($this->createRequestMock(), false),
        );

        $this->statusCode
            ->expects($this->exactly(count($responses)))
            ->method('validate')
            ->will($this->returnValueMap(array(
                array($response1, false),
                array($response2, false),
            )));

        $this->statusCodeSubscriber->onMultiRequestSent($event = $this->createMultiRequestSentEvent(null, $responses));

        $this->assertFalse($event->hasResponses());
        $this->assertCount(count($responses), $event->getExceptions());
    }

    /**
     * {@inheritdoc}
     */
    protected function createRequestMock()
    {
        $request = parent::createRequestMock();
        $request
            ->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue('http://egeloen.fr'));

        return $request;
    }

    /**
     * Creates a response mock.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface|null $internalRequest The internal request.
     * @param boolean                                                  $valid           TRUE if the status code is valid else FALSE.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject The response mock.
     */
    protected function createResponseMock(InternalRequestInterface $internalRequest = null, $valid = true)
    {
        $response = parent::createResponseMock();
        $response
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo('request'))
            ->will($this->returnValue($internalRequest));

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
        return $this->createMock('Ivory\HttpAdapter\Event\StatusCode\StatusCodeInterface');
    }
}
