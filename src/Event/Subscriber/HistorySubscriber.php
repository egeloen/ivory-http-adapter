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
use Ivory\HttpAdapter\Event\PostSendEvent;
use Ivory\HttpAdapter\Event\History\Journal;
use Ivory\HttpAdapter\Event\History\JournalInterface;

/**
 * History subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class HistorySubscriber extends AbstractTimerSubscriber
{
    /** @var \Ivory\HttpAdapter\Event\History\JournalInterface */
    protected $journal;

    /**
     * Creates an history subscriber.
     *
     * @param \Ivory\HttpAdapter\Event\History\JournalInterface $journal
     */
    public function __construct(JournalInterface $journal = null)
    {
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
     * {@inheritdoc}
     */
    public function onPostSend(PostSendEvent $event)
    {
        parent::onPostSend($event);

        $this->journal->record($event->getRequest(), $event->getResponse(), $this->time);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::PRE_SEND  => array('onPreSend', 100),
            Events::POST_SEND => array('onPostSend', 100),
        );
    }
}
