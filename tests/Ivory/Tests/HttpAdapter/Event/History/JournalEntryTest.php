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

use Ivory\HttpAdapter\Event\History\JournalEntry;

/**
 * Journal entry test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class JournalEntryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\Event\History\JournalEntry */
    protected $journalEntry;

    /** @var \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $request;

    /** @var \Ivory\HttpAdapter\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $response;

    /** @var float */
    protected $time;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->journalEntry = new JournalEntry(
            $this->request = $this->getMock('Ivory\HttpAdapter\Message\InternalRequestInterface'),
            $this->response = $this->getMock('Ivory\HttpAdapter\Message\ResponseInterface'),
            $this->time = 1.234
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->journalEntry);
        unset($this->request);
        unset($this->response);
        unset($this->time);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->request, $this->journalEntry->getRequest());
        $this->assertSame($this->response, $this->journalEntry->getResponse());
        $this->assertSame($this->time, $this->journalEntry->getTime());
    }
}
