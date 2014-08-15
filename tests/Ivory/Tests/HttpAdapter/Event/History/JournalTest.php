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
        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\History\JournalEntryFactory',
            $this->journal->getJournalEntryFactory()
        );

        $this->assertSame(10, $this->journal->getLimit());

        $this->assertFalse($this->journal->hasEntries());
        $this->assertEmpty($this->journal->getEntries());

        $this->assertEmpty($this->journal);
        $this->assertEmpty(iterator_to_array($this->journal));
    }

    public function testInitialState()
    {
        $this->journal = new Journal($journalEntryFactory = $this->createJournalEntryFactoryMock());

        $this->assertSame($journalEntryFactory, $this->journal->getJournalEntryFactory());
    }

    public function testSetLimit()
    {
        $this->journal->setLimit($limit = 5);

        $this->assertSame($limit, $this->journal->getLimit());
    }

    public function testSetEntries()
    {
        $this->journal->setEntries(array($this->createJournalEntryMock()));
        $this->journal->setEntries(array(
            $entry1 = $this->createJournalEntryMock(),
            $entry2 = $this->createJournalEntryMock(),
        ));

        $this->assertTrue($this->journal->hasEntries());
        $this->assertSame(array($entry1, $entry2), $this->journal->getEntries());

        $this->assertCount(2, $this->journal);
        $this->assertSame(array($entry2, $entry1), iterator_to_array($this->journal));
    }

    public function testAddEntries()
    {
        $this->journal->setEntries(array($entry1 = $this->createJournalEntryMock()));
        $this->journal->addEntries(array(
            $entry2 = $this->createJournalEntryMock(),
            $entry3 = $this->createJournalEntryMock(),
        ));

        $this->assertTrue($this->journal->hasEntries());
        $this->assertSame(array($entry1, $entry2, $entry3), $this->journal->getEntries());

        $this->assertCount(3, $this->journal);
        $this->assertSame(array($entry3, $entry2, $entry1), iterator_to_array($this->journal));
    }

    public function testRemoveEntries()
    {
        $this->journal->setEntries($entries = array($this->createJournalEntryMock()));
        $this->journal->removeEntries($entries);

        $this->assertFalse($this->journal->hasEntries());
        $this->assertEmpty($this->journal->getEntries());

        $this->assertCount(0, $this->journal);
        $this->assertEmpty(iterator_to_array($this->journal));
    }

    public function testAddEntry()
    {
        $this->journal->addEntry($entry = $this->createJournalEntryMock());

        $this->assertTrue($this->journal->hasEntries());
        $this->assertTrue($this->journal->hasEntry($entry));
        $this->assertSame(array($entry), $this->journal->getEntries());

        $this->assertCount(1, $this->journal);
        $this->assertSame(array($entry), iterator_to_array($this->journal));
    }

    public function testAddEntryExceedLimit()
    {
        $this->journal->addEntry($this->createJournalEntryMock());

        $entries = array();

        for ($i = 0; $i < $this->journal->getLimit(); $i++) {
            $this->journal->addEntry($entries[] = $this->createJournalEntryMock());
        }

        $this->assertSame($entries, $this->journal->getEntries());
    }

    public function testRemoveEntry()
    {
        $this->journal->addEntry($entry = $this->createJournalEntryMock());
        $this->journal->removeEntry($entry);

        $this->assertFalse($this->journal->hasEntries());
        $this->assertFalse($this->journal->hasEntry($entry));
        $this->assertEmpty($this->journal->getEntries());

        $this->assertCount(0, $this->journal);
        $this->assertEmpty(iterator_to_array($this->journal));
    }

    public function testRecord()
    {
        $this->journal->record(
            $request = $this->createRequestMock(),
            $response = $this->createResponseMock(),
            $time = 1.234
        );

        $this->assertTrue($this->journal->hasEntries());

        $entries = $this->journal->getEntries();

        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);

        $this->assertSame($entries[0]->getRequest(), $request);
        $this->assertSame($entries[0]->getResponse(), $response);
        $this->assertSame($entries[0]->getTime(), $time);

        $this->assertCount(1, $this->journal);
        $this->assertNotEmpty(iterator_to_array($this->journal));
    }

    public function testClear()
    {
        $this->journal->setEntries(array($this->createJournalEntryMock()));
        $this->journal->clear();

        $this->assertFalse($this->journal->hasEntries());
        $this->assertEmpty($this->journal->getEntries());

        $this->assertEmpty($this->journal);
        $this->assertEmpty(iterator_to_array($this->journal));
    }

    /**
     * Creates a request mock.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject The request mock.
     */
    protected function createRequestMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
    }

    /**
     * Creates a response mock.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject The response mock.
     */
    protected function createResponseMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }

    /**
     * Creates a journal entry factory mock.
     *
     * @return \Ivory\HttpAdapter\Event\History\JournalEntryFactoryInterface|\PHPUnit_Framework_MockObject_MockObject The journal entry factory mock.
     */
    protected function createJournalEntryFactoryMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Event\History\JournalEntryFactoryInterface');
    }

    /**
     * Creates a journal entry mock.
     *
     * @return \Ivory\HttpAdapter\Event\History\JournalEntryInterface|\PHPUnit_Framework_MockObject_MockObject The journal entry mock.
     */
    protected function createJournalEntryMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Event\History\JournalEntryInterface');
    }
}
