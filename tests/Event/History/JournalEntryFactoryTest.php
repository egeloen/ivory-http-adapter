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

use Ivory\HttpAdapter\Event\History\JournalEntryFactory;

/**
 * Journal entry factory test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class JournalEntryFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\Event\History\JournalEntryFactory */
    protected $journalEntryFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->journalEntryFactory = new JournalEntryFactory();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->journalEntryFactory);
    }

    public function testCreate()
    {
        $entry = $this->journalEntryFactory->create(
            $request = $this->getMock('Ivory\HttpAdapter\Message\InternalRequestInterface'),
            $response = $this->getMock('Ivory\HttpAdapter\Message\ResponseInterface'),
            $time = 1.234
        );

        $this->assertSame($request, $entry->getRequest());
        $this->assertSame($response, $entry->getResponse());
        $this->assertSame($time, $entry->getTime());
    }
}
