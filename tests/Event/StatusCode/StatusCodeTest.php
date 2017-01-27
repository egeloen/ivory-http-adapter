<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\StatusCode;

use Ivory\HttpAdapter\Event\StatusCode\StatusCode;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class StatusCodeTest extends AbstractTestCase
{
    /**
     * @var StatusCode
     */
    private $statusCode;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->statusCode = new StatusCode();
    }

    /**
     * @param int  $statusCode
     * @param bool $expected
     *
     * @dataProvider validateProvider
     */
    public function testValidate($statusCode, $expected)
    {
        $this->assertSame($expected, $this->statusCode->validate($this->createResponseMock($statusCode)));
    }

    /**
     * @return array
     */
    public function validateProvider()
    {
        return [
            [100, true],
            [200, true],
            [300, true],
            [400, false],
            [500, false],
        ];
    }

    /**
     * @param int $statusCode
     *
     * @return ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createResponseMock($statusCode)
    {
        $response = $this->createMock('Ivory\HttpAdapter\Message\ResponseInterface');
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue($statusCode));

        return $response;
    }
}
