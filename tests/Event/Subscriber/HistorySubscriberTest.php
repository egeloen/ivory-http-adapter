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
use Ivory\HttpAdapter\Event\History\JournalInterface;
use Ivory\HttpAdapter\Event\Subscriber\HistorySubscriber;
use Ivory\HttpAdapter\Event\Timer\TimerInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class HistorySubscriberTest extends AbstractSubscriberTest
{
    /**
     * @var HistorySubscriber
     */
    private $historySubscriber;

    /**
     * @var JournalInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $journal;

    /**
     * @var TimerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $timer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->historySubscriber = new HistorySubscriber(
            $this->journal = $this->createJournalMock(),
            $this->timer = $this->createTimerMock()
        );
    }

    public function testDefaultState()
    {
        $this->historySubscriber = new HistorySubscriber();

        $this->assertInstanceOf('Ivory\HttpAdapter\Event\Subscriber\AbstractTimerSubscriber', $this->historySubscriber);
        $this->assertInstanceOf('Ivory\HttpAdapter\Event\History\Journal', $this->historySubscriber->getJournal());
        $this->assertInstanceOf('Ivory\HttpAdapter\Event\Timer\Timer', $this->historySubscriber->getTimer());
    }

    public function testInitialState()
    {
        $this->assertSame($this->journal, $this->historySubscriber->getJournal());
        $this->assertSame($this->timer, $this->historySubscriber->getTimer());
    }

    public function testSubscribedEvents()
    {
        $events = HistorySubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::REQUEST_CREATED, $events);
        $this->assertSame(['onRequestCreated', 100], $events[Events::REQUEST_CREATED]);

        $this->assertArrayHasKey(Events::REQUEST_SENT, $events);
        $this->assertSame(['onRequestSent', 100], $events[Events::REQUEST_SENT]);

        $this->assertArrayHasKey(Events::MULTI_REQUEST_CREATED, $events);
        $this->assertSame(['onMultiRequestCreated', 100], $events[Events::MULTI_REQUEST_CREATED]);

        $this->assertArrayHasKey(Events::MULTI_REQUEST_SENT, $events);
        $this->assertSame(['onMultiRequestSent', 100], $events[Events::MULTI_REQUEST_SENT]);
    }

    public function testRequestSentEvent()
    {
        $this->timer
            ->expects($this->once())
            ->method('start')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue($startedRequest = $this->createRequestMock()));

        $this->timer
            ->expects($this->once())
            ->method('stop')
            ->with($this->identicalTo($startedRequest))
            ->will($this->returnValue($stoppedRequest = $this->createRequestMock()));

        $this->journal
            ->expects($this->once())
            ->method('record')
            ->with(
                $this->identicalTo($stoppedRequest),
                $this->identicalTo($response = $this->createResponseMock())
            );

        $this->historySubscriber->onRequestCreated($this->createRequestCreatedEvent(null, $request));
        $this->historySubscriber->onRequestSent($event = $this->createRequestSentEvent(null, $startedRequest, $response));

        $this->assertSame($stoppedRequest, $event->getRequest());
    }

    public function testMultiRequestSentEvent()
    {
        $requests = [$request1 = $this->createRequestMock(), $request2 = $this->createRequestMock()];

        $this->timer
            ->expects($this->exactly(count($requests)))
            ->method('start')
            ->will($this->returnValueMap([
                [$request1, $startedRequest1 = $this->createRequestMock()],
                [$request2, $startedRequest2 = $this->createRequestMock()],
            ]));

        $responses = [
            $response1 = $this->createResponseMock($startedRequest1),
            $response2 = $this->createResponseMock($startedRequest2),
        ];

        $this->timer
            ->expects($this->exactly(count($responses)))
            ->method('stop')
            ->will($this->returnValueMap([
                [$startedRequest1, $stoppedRequest1 = $this->createRequestMock()],
                [$startedRequest2, $stoppedRequest2 = $this->createRequestMock()],
            ]));

        $this->journal
            ->expects($this->exactly(count($responses)))
            ->method('record')
            ->withConsecutive([$stoppedRequest1, $response1], [$stoppedRequest2, $response2]);

        $response1
            ->expects($this->once())
            ->method('withParameter')
            ->with($this->identicalTo('request'), $this->identicalTo($stoppedRequest1))
            ->will($this->returnValue($stoppedResponse1 = $this->createResponseMock($stoppedRequest1)));

        $response2
            ->expects($this->once())
            ->method('withParameter')
            ->with($this->identicalTo('request'), $this->identicalTo($stoppedRequest2))
            ->will($this->returnValue($stoppedResponse2 = $this->createResponseMock($stoppedRequest2)));

        $this->historySubscriber->onMultiRequestCreated($this->createMultiRequestCreatedEvent(null, $requests));
        $this->historySubscriber->onMultiRequestSent($event = $this->createMultiRequestSentEvent(null, $responses));

        $this->assertSame([$stoppedResponse1, $stoppedResponse2], $event->getResponses());
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
     * @return JournalInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createJournalMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Event\History\JournalInterface');
    }

    /**
     * @return TimerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createTimerMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Event\Timer\TimerInterface');
    }
}
