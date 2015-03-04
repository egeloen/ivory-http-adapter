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
use Ivory\HttpAdapter\Event\Subscriber\HistorySubscriber;
use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * History subscriber test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class HistorySubscriberTest extends AbstractSubscriberTest
{
    /** @var \Ivory\HttpAdapter\Event\Subscriber\HistorySubscriber */
    private $historySubscriber;

    /** @var \Ivory\HttpAdapter\Event\History\JournalInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $journal;

    /** @var \Ivory\HttpAdapter\Event\Timer\TimerInterface|\PHPUnit_Framework_MockObject_MockObject */
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

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->timer);
        unset($this->journal);
        unset($this->historySubscriber);
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

    public function testSetJournal()
    {
        $this->historySubscriber->setJournal($journal = $this->createJournalMock());

        $this->assertSame($journal, $this->historySubscriber->getJournal());
    }

    public function testSubscribedEvents()
    {
        $events = HistorySubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::PRE_SEND, $events);
        $this->assertSame(array('onPreSend', 100), $events[Events::PRE_SEND]);

        $this->assertArrayHasKey(Events::POST_SEND, $events);
        $this->assertSame(array('onPostSend', 100), $events[Events::POST_SEND]);

        $this->assertArrayHasKey(Events::MULTI_PRE_SEND, $events);
        $this->assertSame(array('onMultiPreSend', 100), $events[Events::MULTI_PRE_SEND]);

        $this->assertArrayHasKey(Events::MULTI_POST_SEND, $events);
        $this->assertSame(array('onMultiPostSend', 100), $events[Events::MULTI_POST_SEND]);
    }

    public function testPostSendEvent()
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

        $this->historySubscriber->onPreSend($this->createPreSendEvent(null, $request));
        $this->historySubscriber->onPostSend($event = $this->createPostSendEvent(null, $startedRequest, $response));

        $this->assertSame($stoppedRequest, $event->getRequest());
    }

    public function testMultiPostSendEvent()
    {
        $requests = array($request1 = $this->createRequestMock(), $request2 = $this->createRequestMock());

        $this->timer
            ->expects($this->exactly(count($requests)))
            ->method('start')
            ->will($this->returnValueMap(array(
                array($request1, $startedRequest1 = $this->createRequestMock()),
                array($request2, $startedRequest2 = $this->createRequestMock()),
            )));

        $responses = array(
            $response1 = $this->createResponseMock($startedRequest1),
            $response2 = $this->createResponseMock($startedRequest2),
        );

        $this->timer
            ->expects($this->exactly(count($responses)))
            ->method('stop')
            ->will($this->returnValueMap(array(
                array($startedRequest1, $stoppedRequest1 = $this->createRequestMock()),
                array($startedRequest2, $stoppedRequest2 = $this->createRequestMock()),
            )));

        $this->journal
            ->expects($this->exactly(count($responses)))
            ->method('record')
            ->withConsecutive(array($stoppedRequest1, $response1), array($stoppedRequest2, $response2));

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

        $this->historySubscriber->onMultiPreSend($this->createMultiPreSendEvent(null, $requests));
        $this->historySubscriber->onMultiPostSend($event = $this->createMultiPostSendEvent(null, $responses));

        $this->assertSame(array($stoppedResponse1, $stoppedResponse2), $event->getResponses());
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
     * Creates a journal mock.
     *
     * @return \Ivory\HttpAdapter\Event\History\JournalInterface|\PHPUnit_Framework_MockObject_MockObject The journal mock.
     */
    private function createJournalMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Event\History\JournalInterface');
    }

    /**
     * Creates a timer mock.
     *
     * @return \Ivory\HttpAdapter\Event\Timer\TimerInterface|\PHPUnit_Framework_MockObject_MockObject The timer mock.
     */
    private function createTimerMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Event\Timer\TimerInterface');
    }
}
