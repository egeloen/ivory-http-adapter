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

use Ivory\HttpAdapter\Event\PostSendEvent;
use Ivory\HttpAdapter\Event\History\Journal;

/**
 * History subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class HistorySubscriber extends AbstractTimerSubscriber
{
    /** @var \Ivory\HttpAdapter\Event\History\Journal */
    protected $journal;

    /**
     * Creates an history subscriber.
     *
     * @param \Ivory\HttpAdapter\Event\History\Journal $journal
     */
    public function __construct(Journal $journal = null)
    {
        $this->setJournal($journal ?: new Journal());
    }

    /**
     * Gets the journal.
     *
     * @return \Ivory\HttpAdapter\Event\History\Journal The journal.
     */
    public function getJournal()
    {
        return $this->journal;
    }

    /**
     * Sets the journal.
     *
     * @param \Ivory\HttpAdapter\Event\History\Journal $journal The journal.
     */
    public function setJournal(Journal $journal)
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
}
