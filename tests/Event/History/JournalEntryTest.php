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
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class JournalEntryTest extends AbstractTestCase
{
    /**
     * @var JournalEntry
     */
    private $journalEntry;

    /**
     * @var InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     * @var ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $response;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->journalEntry = new JournalEntry(
            $this->request = $this->createMock('Ivory\HttpAdapter\Message\InternalRequestInterface'),
            $this->response = $this->createMock('Ivory\HttpAdapter\Message\ResponseInterface')
        );
    }

    public function testDefaultState()
    {
        $this->assertSame($this->request, $this->journalEntry->getRequest());
        $this->assertSame($this->response, $this->journalEntry->getResponse());
    }

    public function testSetRequest()
    {
        $this->journalEntry->setRequest($request = $this->createRequestMock());

        $this->assertSame($request, $this->journalEntry->getRequest());
    }

    public function setResponse()
    {
        $this->journalEntry->setResponse($response = $this->createResponseMock());

        $this->assertSame($response, $this->journalEntry->getResponse());
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
}
