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

/**
 * History subscriber test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class HistorySubscriberTest extends AbstractSubscriberTest
{
    /** @var \Ivory\HttpAdapter\Event\Subscriber\HistorySubscriber */
    protected $historySubscriber;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->historySubscriber = new HistorySubscriber();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->historySubscriber);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Event\History\Journal', $this->historySubscriber->getJournal());
    }

    public function testInitialState()
    {
        $this->historySubscriber = new HistorySubscriber($journal = $this->createJournalMock());

        $this->assertSame($journal, $this->historySubscriber->getJournal());
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
    }

    public function testPostSendEvent()
    {
        $this->historySubscriber->setJournal($journal = $this->createJournalMock());

        $journal
            ->expects($this->once())
            ->method('record')
            ->with(
                $this->identicalTo($request = $this->createRequestMock()),
                $this->identicalTo($response = $this->createResponseMock()),
                $this->isType('float')
            );

        $this->historySubscriber->onPreSend($this->createPreSendEvent(null, $request));
        $this->historySubscriber->onPostSend($this->createPostSendEvent(null, $request, $response));
    }

    /**
     * Creates a journal mock.
     *
     * @return \Ivory\HttpAdapter\Event\History\JournalInterface|\PHPUnit_Framework_MockObject_MockObject The journal mock.
     */
    protected function createJournalMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Event\History\JournalInterface');
    }
}
