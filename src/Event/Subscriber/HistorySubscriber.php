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
use Ivory\HttpAdapter\Event\History\Journal;
use Ivory\HttpAdapter\Event\History\JournalInterface;
use Ivory\HttpAdapter\Event\MultiRequestCreatedEvent;
use Ivory\HttpAdapter\Event\MultiRequestSentEvent;
use Ivory\HttpAdapter\Event\RequestCreatedEvent;
use Ivory\HttpAdapter\Event\RequestSentEvent;
use Ivory\HttpAdapter\Event\Timer\TimerInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class HistorySubscriber extends AbstractTimerSubscriber
{
    /**
     * @var JournalInterface
     */
    private $journal;

    /**
     * @param JournalInterface|null $journal
     * @param TimerInterface|null   $timer
     */
    public function __construct(JournalInterface $journal = null, TimerInterface $timer = null)
    {
        parent::__construct($timer);

        $this->journal = $journal ?: new Journal();
    }

    /**
     * @return JournalInterface
     */
    public function getJournal()
    {
        return $this->journal;
    }

    /**
     * @param RequestCreatedEvent $event
     */
    public function onRequestCreated(RequestCreatedEvent $event)
    {
        $event->setRequest($this->getTimer()->start($event->getRequest()));
    }

    /**
     * @param RequestSentEvent $event
     */
    public function onRequestSent(RequestSentEvent $event)
    {
        $event->setRequest($this->record($event->getRequest(), $event->getResponse()));
    }

    /**
     * @param MultiRequestCreatedEvent $event
     */
    public function onMultiRequestCreated(MultiRequestCreatedEvent $event)
    {
        foreach ($event->getRequests() as $request) {
            $event->removeRequest($request);
            $event->addRequest($this->getTimer()->start($request));
        }
    }

    /**
     * @param MultiRequestSentEvent $event
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
        return [
            Events::REQUEST_CREATED       => ['onRequestCreated', 100],
            Events::REQUEST_SENT          => ['onRequestSent', 100],
            Events::MULTI_REQUEST_CREATED => ['onMultiRequestCreated', 100],
            Events::MULTI_REQUEST_SENT    => ['onMultiRequestSent', 100],
        ];
    }

    /**
     * @param InternalRequestInterface $internalRequest
     * @param ResponseInterface        $response
     *
     * @return InternalRequestInterface
     */
    private function record(InternalRequestInterface $internalRequest, ResponseInterface $response)
    {
        $internalRequest = $this->getTimer()->stop($internalRequest);
        $this->journal->record($internalRequest, $response);

        return $internalRequest;
    }
}
