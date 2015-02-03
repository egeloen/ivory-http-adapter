<?php

/**
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
interface JournalInterface extends \Countable, \IteratorAggregate
{
    /**
     * Records an entry.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request  The request.
     * @param \Ivory\HttpAdapter\Message\ResponseInterface        $response The response.
     *
     * @return void No return value.
     */
    public function record(InternalRequestInterface $request, ResponseInterface $response);

    /**
     * Clears the journal.
     *
     * @return void No return value.
     */
    public function clear();

    /**
     * Checks if there are entries.
     *
     * @return boolean TRUE if there are entries else FALSE.
     */
    public function hasEntries();

    /**
     * Gets the entries.
     *
     * @return array The entries.
     */
    public function getEntries();

    /**
     * Sets the entries.
     *
     * @param array $entries The entries.
     *
     * @return void No return value.
     */
    public function setEntries(array $entries);

    /**
     * Adds the entries.
     *
     * @param array $entries The entries.
     *
     * @return void No return value.
     */
    public function addEntries(array $entries);

    /**
     * Removes the entries.
     *
     * @param array $entries The entries.
     *
     * @return void No return value.
     */
    public function removeEntries(array $entries);

    /**
     * Checks if there is the entry.
     *
     * @param \Ivory\HttpAdapter\Event\History\JournalEntryInterface $entry The entry.
     *
     * @return boolean TRUE if there is the entry else FALSE.
     */
    public function hasEntry(JournalEntryInterface $entry);

    /**
     * Adds an entry.
     *
     * @param \Ivory\HttpAdapter\Event\History\JournalEntryInterface $entry The entry.
     *
     * @return void No return value.
     */
    public function addEntry(JournalEntryInterface $entry);

    /**
     * Removes an entry.
     *
     * @param \Ivory\HttpAdapter\Event\History\JournalEntryInterface $entry The entry.
     *
     * @return void No return value.
     */
    public function removeEntry(JournalEntryInterface $entry);

    /**
     * Gets the limit.
     *
     * @return integer The limit.
     */
    public function getLimit();

    /**
     * Sets the limit.
     *
     * @param integer $limit The limit.
     *
     * @return void No return value.
     */
    public function setLimit($limit);
}
