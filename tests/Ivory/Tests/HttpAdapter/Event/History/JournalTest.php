<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\History;

use Ivory\HttpAdapter\Event\History\Journal;

/**
 * Journal test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class JournalTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\Event\History\Journal */
    protected $journal;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->journal = new Journal();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->journal);
    }

    public function testDefaultState()
    {
        $this->assertSame(10, $this->journal->getLimit());

        $this->assertFalse($this->journal->hasEntries());
        $this->assertEmpty($this->journal->getEntries());
        $this->assertFalse($this->journal->getLastEntry());

        $this->assertEmpty($this->journal);
        $this->assertEmpty(iterator_to_array($this->journal));
    }

    public function testSetLimit()
    {
        $this->journal->setLimit($limit = 5);

        $this->assertSame($limit, $this->journal->getLimit());
    }

    public function testAddSingleEntry()
    {
        $this->journal->addEntry($entry = $this->createJournalEntry());

        $this->assertTrue($this->journal->hasEntries());
        $this->assertSame(array($entry), $this->journal->getEntries());
        $this->assertSame($entry, $this->journal->getLastEntry());

        $this->assertCount(1, $this->journal);
        $this->assertSame(array($entry), iterator_to_array($this->journal));
    }

    public function testAddMultipleEntries()
    {
        $this->journal->addEntry($entry1 = $this->createJournalEntry());
        $this->journal->addEntry($entry2 = $this->createJournalEntry());

        $this->assertTrue($this->journal->hasEntries());
        $this->assertSame(array($entry1, $entry2), $this->journal->getEntries());
        $this->assertSame($entry2, $this->journal->getLastEntry());

        $this->assertCount(2, $this->journal);
        $this->assertSame(array($entry2, $entry1), iterator_to_array($this->journal));
    }

    public function testAddEntriesExceedLimit()
    {
        for ($i = 0; $i < $this->journal->getLimit(); $i++) {
            $this->journal->addEntry($entries[] = $this->createJournalEntry());
        }

        $entries = array();

        for ($i = 0; $i < $this->journal->getLimit(); $i++) {
            $this->journal->addEntry($entries[] = $this->createJournalEntry());
        }

        $this->assertSame($entries, $this->journal->getEntries());
    }

    public function testSingleRecord()
    {
        $this->journal->record($request = $this->createRequest(), $response = $this->createResponse(), $time = 1.234);

        $this->assertTrue($this->journal->hasEntries());
        $this->assertNotEmpty($this->journal->getEntries());
        $this->assertSame($request, $this->journal->getLastEntry()->getRequest());
        $this->assertSame($response, $this->journal->getLastEntry()->getResponse());
        $this->assertSame($time, $this->journal->getLastEntry()->getTime());

        $this->assertCount(1, $this->journal);
        $this->assertNotEmpty(iterator_to_array($this->journal));
    }

    public function testMultipleRecords()
    {
        $this->journal->record($this->createRequest(), $this->createResponse(), 1.234);

        $this->journal->record(
            $request2 = $this->createRequest(),
            $response2 = $this->createResponse(),
            $time2 = 4.567
        );

        $this->assertTrue($this->journal->hasEntries());
        $this->assertNotEmpty($this->journal->getEntries());
        $this->assertSame($request2, $this->journal->getLastEntry()->getRequest());
        $this->assertSame($response2, $this->journal->getLastEntry()->getResponse());
        $this->assertSame($time2, $this->journal->getLastEntry()->getTime());

        $this->assertCount(2, $this->journal);
        $this->assertNotEmpty(iterator_to_array($this->journal));
    }

    public function testClear()
    {
        $this->journal->addEntry($this->createJournalEntry());
        $this->journal->clear();

        $this->assertFalse($this->journal->hasEntries());
        $this->assertEmpty($this->journal->getEntries());

        $this->assertEmpty($this->journal);
        $this->assertEmpty(iterator_to_array($this->journal));
    }

    /**
     * Creates a request.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject The request.
     */
    protected function createRequest()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
    }

    /**
     * Creates a response.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject The response.
     */
    protected function createResponse()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }

    /**
     * Creates a journal entry.
     *
     * @return \Ivory\HttpAdapter\Event\History\JournalEntry|\PHPUnit_Framework_MockObject_MockObject The journal entry.
     */
    protected function createJournalEntry()
    {
        return $this->getMockBuilder('Ivory\HttpAdapter\Event\History\JournalEntry')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
