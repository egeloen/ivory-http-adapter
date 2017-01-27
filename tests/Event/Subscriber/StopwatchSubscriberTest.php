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
use Ivory\HttpAdapter\Event\Subscriber\StopwatchSubscriber;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class StopwatchSubscriberTest extends AbstractSubscriberTest
{
    /**
     * @var StopwatchSubscriber
     */
    private $stopwatchSubscriber;

    /**
     * @var Stopwatch|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stopwatch;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->stopwatchSubscriber = new StopwatchSubscriber(
            $this->stopwatch = $this->createMock('Symfony\Component\Stopwatch\Stopwatch')
        );
    }

    public function testDefaultState()
    {
        $this->assertSame($this->stopwatch, $this->stopwatchSubscriber->getStopwatch());
    }

    public function testSubscribedEvents()
    {
        $events = StopwatchSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::REQUEST_CREATED, $events);
        $this->assertSame(['onRequestCreated', 10000], $events[Events::REQUEST_CREATED]);

        $this->assertArrayHasKey(Events::REQUEST_SENT, $events);
        $this->assertSame(['onRequestSent', -10000], $events[Events::REQUEST_SENT]);

        $this->assertArrayHasKey(Events::REQUEST_ERRORED, $events);
        $this->assertSame(['onRequestErrored', -10000], $events[Events::REQUEST_ERRORED]);

        $this->assertArrayHasKey(Events::MULTI_REQUEST_CREATED, $events);
        $this->assertSame(['onMultiRequestCreated', 10000], $events[Events::MULTI_REQUEST_CREATED]);

        $this->assertArrayHasKey(Events::MULTI_REQUEST_SENT, $events);
        $this->assertSame(['onMultiRequestSent', -10000], $events[Events::MULTI_REQUEST_SENT]);

        $this->assertArrayHasKey(Events::MULTI_REQUEST_ERRORED, $events);
        $this->assertSame(['onMultiResponseErrored', -10000], $events[Events::MULTI_REQUEST_ERRORED]);
    }

    public function testRequestCreatedEvent()
    {
        $this->stopwatch
            ->expects($this->once())
            ->method('start')
            ->with($this->identicalTo('ivory.http_adapter.http_adapter (uri)'));

        $this->stopwatchSubscriber->onRequestCreated($this->createRequestCreatedEvent());
    }

    public function testRequestSentEvent()
    {
        $this->stopwatch
            ->expects($this->once())
            ->method('stop')
            ->with($this->identicalTo('ivory.http_adapter.http_adapter (uri)'));

        $this->stopwatchSubscriber->onRequestSent($this->createRequestSentEvent());
    }

    public function testRequestErroredEvent()
    {
        $this->stopwatch
            ->expects($this->once())
            ->method('stop')
            ->with($this->identicalTo('ivory.http_adapter.http_adapter (uri)'));

        $this->stopwatchSubscriber->onRequestErrored($this->createRequestErroredEvent());
    }

    public function testMultiRequestCreatedEvent()
    {
        $requests = [$this->createRequestMock(), $this->createRequestMock()];

        $this->stopwatch
            ->expects($this->exactly(count($requests)))
            ->method('start')
            ->withConsecutive(
                ['ivory.http_adapter.http_adapter (uri)'],
                ['ivory.http_adapter.http_adapter (uri)']
            );

        $this->stopwatchSubscriber->onMultiRequestCreated($this->createMultiRequestCreatedEvent(null, $requests));
    }

    public function testMultiRequestSentEvent()
    {
        $responses = [
            $response1 = $this->createResponseMock(),
            $response2 = $this->createResponseMock(),
        ];

        $response1
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo('request'))
            ->will($this->returnValue($this->createRequestMock()));

        $response2
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo('request'))
            ->will($this->returnValue($this->createRequestMock()));

        $this->stopwatch
            ->expects($this->exactly(count($responses)))
            ->method('stop')
            ->withConsecutive(
                ['ivory.http_adapter.http_adapter (uri)'],
                ['ivory.http_adapter.http_adapter (uri)']
            );

        $this->stopwatchSubscriber->onMultiRequestSent($this->createMultiRequestSentEvent(null, $responses));
    }

    public function testMultiRequestErroredEvent()
    {
        $exceptions = [$this->createExceptionMock(), $this->createExceptionMock()];

        $responses = [
            $response1 = $this->createResponseMock(),
            $response2 = $this->createResponseMock(),
        ];

        $response1
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo('request'))
            ->will($this->returnValue($this->createRequestMock()));

        $response2
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo('request'))
            ->will($this->returnValue($this->createRequestMock()));

        $this->stopwatch
            ->expects($this->exactly(count($exceptions) + count($responses)))
            ->method('stop')
            ->withConsecutive(
                ['ivory.http_adapter.http_adapter (uri)'],
                ['ivory.http_adapter.http_adapter (uri)'],
                ['ivory.http_adapter.http_adapter (uri)'],
                ['ivory.http_adapter.http_adapter (uri)']
            );

        $this->stopwatchSubscriber->onMultiResponseErrored(
            $this->createMultiRequestErroredEvent(null, $exceptions, $responses)
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function createRequestMock()
    {
        $request = parent::createRequestMock();
        $request
            ->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue('uri'));

        return $request;
    }
}
