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
 * Journal.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Journal implements \Countable, \IteratorAggregate
{
    /** @var integer */
    protected $limit;

    /** @var array */
    protected $entries = array();

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
     * Gets the limit.
     *
     * @return integer The limit.
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Sets the limit.
     *
     * @param integer $limit The limit.
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * Clears the journal.
     */
    public function clear()
    {
        $this->entries = array();
    }

    /**
     * Records an entry.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request  The request.
     * @param \Ivory\HttpAdapter\Message\ResponseInterface        $response The response.
     * @param float                                               $time     The time.
     */
    public function record(InternalRequestInterface $request, ResponseInterface $response, $time)
    {
        $this->addEntry(new JournalEntry($request, $response, $time));
    }

    /**
     * Checks if there are entries.
     *
     * @return boolean TRUE if there are entries else FALSE.
     */
    public function hasEntries()
    {
        return !empty($this->entries);
    }

    /**
     * Gets the entries.
     *
     * @return array The entries.
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * Gets the last entry.
     *
     * @return \Ivory\HttpAdapter\Event\History\JournalEntry|boolen The last entry or false if there is no entry.
     */
    public function getLastEntry()
    {
        return end($this->entries);
    }

    /**
     * Adds an entry.
     *
     * @param \Ivory\HttpAdapter\Event\History\JournalEntry $entry The entry.
     */
    public function addEntry(JournalEntry $entry)
    {
        $this->entries[] = $entry;
        $this->entries = array_slice($this->entries, $this->limit * -1);
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
