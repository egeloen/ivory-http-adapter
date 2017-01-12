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
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * Journal entry test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class JournalEntryTest extends AbstractTestCase
{
    /** @var \Ivory\HttpAdapter\Event\History\JournalEntry */
    private $journalEntry;

    /** @var \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $request;

    /** @var \Ivory\HttpAdapter\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject */
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

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->journalEntry);
        unset($this->request);
        unset($this->response);
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
     * Creates a request mock.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject The request mock.
     */
    private function createRequestMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
    }

    /**
     * Creates a response mock.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject The response mock.
     */
    private function createResponseMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }
}
