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
use Ivory\HttpAdapter\Event\MultiRequestSentEvent;
use Ivory\HttpAdapter\Event\MultiRequestCreatedEvent;
use Ivory\HttpAdapter\Event\RequestSentEvent;
use Ivory\HttpAdapter\Event\RequestCreatedEvent;
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

        $this->journal = $journal ?: new Journal();
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
     * On request created event.
     *
     * @param \Ivory\HttpAdapter\Event\RequestCreatedEvent $event The request created event.
     */
    public function onRequestCreated(RequestCreatedEvent $event)
    {
        $event->setRequest($this->getTimer()->start($event->getRequest()));
    }

    /**
     * On request sent event.
     *
     * @param \Ivory\HttpAdapter\Event\RequestSentEvent $event The request sent event.
     */
    public function onRequestSent(RequestSentEvent $event)
    {
        $event->setRequest($this->record($event->getRequest(), $event->getResponse()));
    }

    /**
     * On multi request created event.
     *
     * @param \Ivory\HttpAdapter\Event\MultiRequestCreatedEvent $event The multi request created event.
     */
    public function onMultiRequestCreated(MultiRequestCreatedEvent $event)
    {
        foreach ($event->getRequests() as $request) {
            $event->removeRequest($request);
            $event->addRequest($this->getTimer()->start($request));
        }
    }

    /**
     * On multi request sent event.
     *
     * @param \Ivory\HttpAdapter\Event\MultiRequestSentEvent $event The multi request sent event.
     */
    public function onMultiRequestSent(MultiRequestSentEvent $event)
    {
        foreach ($event->getResponses() as $response) {
            $request = $this->record($response->getParameter('request'), $response);

            $event->removeResponse($response);
            $event->addResponse($response->withParameter('request', $request));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::REQUEST_CREATED       => array('onRequestCreated', 100),
            Events::REQUEST_SENT          => array('onRequestSent', 100),
            Events::MULTI_REQUEST_CREATED => array('onMultiRequestCreated', 100),
            Events::MULTI_REQUEST_SENT    => array('onMultiRequestSent', 100),
        );
    }

    /**
     * Records a journal entry.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     * @param \Ivory\HttpAdapter\Message\ResponseInterface        $response        The response.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The recorded request.
     */
    private function record(InternalRequestInterface $internalRequest, ResponseInterface $response)
    {
        $internalRequest = $this->getTimer()->stop($internalRequest);
        $this->journal->record($internalRequest, $response);

        return $internalRequest;
    }
}
