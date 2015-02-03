<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\Subscriber;

use Ivory\HttpAdapter\Event\Events;
use Ivory\HttpAdapter\Event\MultiPostSendEvent;
use Ivory\HttpAdapter\Event\MultiPreSendEvent;
use Ivory\HttpAdapter\Event\PostSendEvent;
use Ivory\HttpAdapter\Event\PreSendEvent;
use Ivory\HttpAdapter\Event\History\Journal;
use Ivory\HttpAdapter\Event\History\JournalInterface;
use Ivory\HttpAdapter\Event\Timer\TimerInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * History subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class HistorySubscriber extends AbstractTimerSubscriber
{
    /** @var \Ivory\HttpAdapter\Event\History\JournalInterface */
    private $journal;

    /**
     * Creates an history subscriber.
     *
     * @param \Ivory\HttpAdapter\Event\History\JournalInterface|null $journal The journal
     * @param \Ivory\HttpAdapter\Event\Timer\TimerInterface|null     $timer   The timer.
     */
    public function __construct(JournalInterface $journal = null, TimerInterface $timer = null)
    {
        parent::__construct($timer);

        $this->setJournal($journal ?: new Journal());
    }

    /**
     * Gets the journal.
     *
     * @return \Ivory\HttpAdapter\Event\History\JournalInterface The journal.
     */
    public function getJournal()
    {
        return $this->journal;
    }

    /**
     * Sets the journal.
     *
     * @param \Ivory\HttpAdapter\Event\History\JournalInterface $journal The journal.
     */
    public function setJournal(JournalInterface $journal)
    {
        $this->journal = $journal;
    }

    /**
     * On pre send event.
     *
     * @param \Ivory\HttpAdapter\Event\PreSendEvent $event The pre send event.
     */
    public function onPreSend(PreSendEvent $event)
    {
        $this->getTimer()->start($event->getRequest());
    }

    /**
     * On post send event.
     *
     * @param \Ivory\HttpAdapter\Event\PostSendEvent $event Th post send event.
     */
    public function onPostSend(PostSendEvent $event)
    {
        $this->record($event->getRequest(), $event->getResponse());
    }

    /**
     * On multi pre send event.
     *
     * @param \Ivory\HttpAdapter\Event\MultiPreSendEvent $event The multi pre send event.
     */
    public function onMultiPreSend(MultiPreSendEvent $event)
    {
        foreach ($event->getRequests() as $request) {
            $this->getTimer()->start($request);
        }
    }

    /**
     * On multi post send event.
     *
     * @param \Ivory\HttpAdapter\Event\MultiPostSendEvent $event The multi post send event.
     */
    public function onMultiPostSend(MultiPostSendEvent $event)
    {
        foreach ($event->getResponses() as $response) {
            $this->record($response->getParameter('request'), $response);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::PRE_SEND        => array('onPreSend', 100),
            Events::POST_SEND       => array('onPostSend', 100),
            Events::MULTI_PRE_SEND  => array('onMultiPreSend', 100),
            Events::MULTI_POST_SEND => array('onMultiPostSend', 100),
        );
    }

    /**
     * Records a journal entry.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     * @param \Ivory\HttpAdapter\Message\ResponseInterface        $response        The response.
     */
    private function record(InternalRequestInterface $internalRequest, ResponseInterface $response)
    {
        $this->getTimer()->stop($internalRequest);
        $this->journal->record($internalRequest, $response);
    }
}
