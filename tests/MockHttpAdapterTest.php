<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter;

use Ivory\HttpAdapter\MockHttpAdapter;

class MockHttpAdapterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\MockHttpAdapter */
    private $sut;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->sut = new MockHttpAdapter();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->sut);
    }

    /**
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Mock queue is empty
     */
    public function test_no_response_in_queue_leads_to_exception_when_sending_request()
    {
        $this->sut->get('http://google.com');
    }

    public function test_any_request_gets_queued_response_as_result()
    {
        $expectedResponse = $this->givenAResponseWithContent('Hello world');
        $this->sut->appendResponse($expectedResponse);
        $resultResponse = $this->sut->get('http://google.com');
        $this->assertSame($resultResponse, $expectedResponse);
    }

    public function test_count_returns_number_of_queued_responses()
    {
        $this->assertCount(0, $this->sut);

        $this->sut->appendResponse($this->givenAResponseWithContent('Hello world'));
        $this->sut->appendResponse($this->givenAResponseWithContent('I am happy'));

        $this->assertCount(2, $this->sut);
    }

    public function test_response_used_is_dequeued()
    {
        $this->sut->appendResponse($this->givenAResponseWithContent('Hello world'));
        $this->assertCount(1, $this->sut);
        $this->sut->get('http://google.com');
        $this->assertCount(0, $this->sut);
    }

    private function givenAResponseWithContent($content)
    {
        $messageFactory = new \Ivory\HttpAdapter\Message\MessageFactory();

        return $messageFactory->createResponse(
            200,
            \Ivory\HttpAdapter\Message\RequestInterface::PROTOCOL_VERSION_1_1,
            ['Content-Type: application/json'],
            $content
        );
    }
}
