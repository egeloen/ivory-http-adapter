<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\History;

use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * {@inheritdoc}
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Journal implements JournalInterface
{
    /** @var \Ivory\HttpAdapter\Event\History\JournalEntryFactoryInterface */
    private $journalEntryFactory;

    /** @var array */
    private $entries = array();

    /** @var integer */
    private $limit = 10;

    /**
     * Creates a journal.
     *
     * @param \Ivory\HttpAdapter\Event\History\JournalEntryFactoryInterface|null $journalEntryFactory The journal entry factory.
     */
    public function __construct(JournalEntryFactoryInterface $journalEntryFactory = null)
    {
        $this->setJournalEntryFactory($journalEntryFactory ?: new JournalEntryFactory());
    }

    /**
     * Gets the journal entry factory.
     *
     * @return \Ivory\HttpAdapter\Event\History\JournalEntryFactoryInterface The journal entry factory.
     */
    public function getJournalEntryFactory()
    {
        return $this->journalEntryFactory;
    }

    /**
     * Sets the journal entry factory.
     *
     * @param \Ivory\HttpAdapter\Event\History\JournalEntryFactoryInterface $journalEntryFactory The journal entry factory.
     */
    public function setJournalEntryFactory(JournalEntryFactoryInterface $journalEntryFactory)
    {
        $this->journalEntryFactory = $journalEntryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function record(InternalRequestInterface $request, ResponseInterface $response)
    {
        $this->addEntry($this->journalEntryFactory->create($request, $response));
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->entries = array();
    }

    /**
     * {@inheritdoc}
     */
    public function hasEntries()
    {
        return !empty($this->entries);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * {@inheritdoc}
     */
    public function setEntries(array $entries)
    {
        $this->clear();
        $this->addEntries($entries);
    }

    /**
     * {@inheritdoc}
     */
    public function addEntries(array $entries)
    {
        foreach ($entries as $entry) {
            $this->addEntry($entry);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeEntries(array $entries)
    {
        foreach ($entries as $entry) {
            $this->removeEntry($entry);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasEntry(JournalEntryInterface $entry)
    {
        return array_search($entry, $this->entries, true) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function addEntry(JournalEntryInterface $entry)
    {
        if (!$this->hasEntry($entry)) {
            $this->entries[] = $entry;
            $this->entries = array_slice($this->entries, $this->limit * -1);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeEntry(JournalEntryInterface $entry)
    {
        if ($this->hasEntry($entry)) {
            unset($this->entries[array_search($entry, $this->entries, true)]);
            $this->entries = array_values($this->entries);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * {@inheritdoc}
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->entries);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator(array_reverse($this->entries));
    }
}
