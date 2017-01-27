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
 * @author GeLo <geloen.eric@gmail.com>
 */
interface JournalInterface extends \Countable, \IteratorAggregate
{
    /**
     * @param InternalRequestInterface $request
     * @param ResponseInterface        $response
     */
    public function record(InternalRequestInterface $request, ResponseInterface $response);

    public function clear();

    /**
     * @return bool
     */
    public function hasEntries();

    /**
     * @return JournalEntryInterface[]
     */
    public function getEntries();

    /**
     * @param JournalEntryInterface[] $entries
     */
    public function setEntries(array $entries);

    /**
     * @param JournalEntryInterface[] $entries
     */
    public function addEntries(array $entries);

    /**
     * @param JournalEntryInterface[] $entries
     */
    public function removeEntries(array $entries);

    /**
     * @param JournalEntryInterface $entry
     *
     * @return bool
     */
    public function hasEntry(JournalEntryInterface $entry);

    /**
     * @param JournalEntryInterface $entry
     */
    public function addEntry(JournalEntryInterface $entry);

    /**
     * @param JournalEntryInterface $entry
     */
    public function removeEntry(JournalEntryInterface $entry);

    /**
     * @return int
     */
    public function getLimit();

    /**
     * @param int $limit
     */
    public function setLimit($limit);
}
