<?php

/**
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\StatusCode;

use Ivory\HttpAdapter\Event\StatusCode\StatusCode;

/**
 * Status code test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StatusCodeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\Event\StatusCode\StatusCode */
    private $statusCode;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->statusCode = new StatusCode();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->statusCode);
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($statusCode, $expected)
    {
        $this->assertSame($expected, $this->statusCode->validate($this->createResponseMock($statusCode)));
    }

    /**
     * Gets the validate provider.
     *
     * @return array The validate provider.
     */
    public function validateProvider()
    {
        return array(
            array(100, true),
            array(200, true),
            array(300, true),
            array(400, false),
            array(500, false),
        );
    }

    /**
     * Creates a response mock.
     *
     * @param integer $statusCode The status code.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject The response mock.
     */
    private function createResponseMock($statusCode)
    {
        $response = $this->getMock('Ivory\HttpAdapter\Message\ResponseInterface');
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue($statusCode));

        return $response;
    }
}
