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
use Ivory\HttpAdapter\Event\History\JournalEntryFactoryInterface;
use Ivory\HttpAdapter\Event\History\JournalEntryInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class JournalTest extends AbstractTestCase
{
    /**
     * @var Journal
     */
    private $journal;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->journal = new Journal();
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
        $this->journal->setEntries([$this->createJournalEntryMock()]);
        $this->journal->setEntries([
            $entry1 = $this->createJournalEntryMock(),
            $entry2 = $this->createJournalEntryMock(),
        ]);

        $this->assertTrue($this->journal->hasEntries());
        $this->assertSame([$entry1, $entry2], $this->journal->getEntries());

        $this->assertCount(2, $this->journal);
        $this->assertSame([$entry2, $entry1], iterator_to_array($this->journal));
    }

    public function testAddEntries()
    {
        $this->journal->setEntries([$entry1 = $this->createJournalEntryMock()]);
        $this->journal->addEntries([
            $entry2 = $this->createJournalEntryMock(),
            $entry3 = $this->createJournalEntryMock(),
        ]);

        $this->assertTrue($this->journal->hasEntries());
        $this->assertSame([$entry1, $entry2, $entry3], $this->journal->getEntries());

        $this->assertCount(3, $this->journal);
        $this->assertSame([$entry3, $entry2, $entry1], iterator_to_array($this->journal));
    }

    public function testRemoveEntries()
    {
        $this->journal->setEntries($entries = [$this->createJournalEntryMock()]);
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
        $this->assertSame([$entry], $this->journal->getEntries());

        $this->assertCount(1, $this->journal);
        $this->assertSame([$entry], iterator_to_array($this->journal));
    }

    public function testAddEntryExceedLimit()
    {
        $this->journal->addEntry($this->createJournalEntryMock());

        $entries = [];

        for ($i = 0; $i < $this->journal->getLimit(); ++$i) {
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
        $this->journal->record($request = $this->createRequestMock(), $response = $this->createResponseMock());

        $this->assertTrue($this->journal->hasEntries());

        $entries = $this->journal->getEntries();

        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);

        $this->assertSame($entries[0]->getRequest(), $request);
        $this->assertSame($entries[0]->getResponse(), $response);

        $this->assertCount(1, $this->journal);
        $this->assertNotEmpty(iterator_to_array($this->journal));
    }

    public function testClear()
    {
        $this->journal->setEntries([$this->createJournalEntryMock()]);
        $this->journal->clear();

        $this->assertFalse($this->journal->hasEntries());
        $this->assertEmpty($this->journal->getEntries());

        $this->assertEmpty($this->journal);
        $this->assertEmpty(iterator_to_array($this->journal));
    }

    /**
     * @return InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createRequestMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
    }

    /**
     * @return ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createResponseMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }

    /**
     * @return JournalEntryFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createJournalEntryFactoryMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Event\History\JournalEntryFactoryInterface');
    }

    /**
     * @return JournalEntryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createJournalEntryMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Event\History\JournalEntryInterface');
    }
}
