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
    /** @var array */
    protected $entries = array();

    /** @var integer */
    protected $limit;

    /**
     * Creates a journal.
     *
     * @param integer $limit The limit.
     */
    public function __construct($limit = 10)
    {
        $this->setLimit($limit);
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
    public function record(InternalRequestInterface $request, ResponseInterface $response, $time)
    {
        $this->addEntry(new JournalEntry($request, $response, $time));
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
