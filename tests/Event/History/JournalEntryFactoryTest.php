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
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class JournalEntryFactoryTest extends AbstractTestCase
{
    /**
     * @var JournalEntryFactory
     */
    private $journalEntryFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->journalEntryFactory = new JournalEntryFactory();
    }

    public function testCreate()
    {
        $entry = $this->journalEntryFactory->create(
            $request = $this->createMock('Ivory\HttpAdapter\Message\InternalRequestInterface'),
            $response = $this->createMock('Ivory\HttpAdapter\Message\ResponseInterface')
        );

        $this->assertSame($request, $entry->getRequest());
        $this->assertSame($response, $entry->getResponse());
    }
}
