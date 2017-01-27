<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\Formatter;

use Ivory\HttpAdapter\Event\Formatter\Formatter;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FormatterTest extends AbstractTestCase
{
    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->formatter = new Formatter();
    }

    public function testFormatRequest()
    {
        $this->assertSame(
            [
                'protocol_version' => '1.1',
                'uri'              => 'http://egeloen.fr',
                'method'           => 'GET',
                'headers'          => ['foo' => 'bar'],
                'body'             => 'foo=bar',
                'datas'            => ['baz' => 'bat'],
                'files'            => ['bit' => __FILE__],
                'parameters'       => ['ban' => 'bor'],
            ],
            $this->formatter->formatRequest($this->createRequestMock())
        );
    }

    public function testFormatResponse()
    {
        $this->assertSame(
            [
                'protocol_version' => '1.1',
                'status_code'      => 200,
                'reason_phrase'    => 'OK',
                'headers'          => ['bal' => 'bol'],
                'body'             => 'body',
                'parameters'       => ['bil' => 'bob'],
            ],
            $this->formatter->formatResponse($this->createResponseMock())
        );
    }

    public function testFormatException()
    {
        $this->assertSame(
            [
                'code'    => 123,
                'message' => 'message',
                'line'    => 234,
                'file'    => __FILE__,
            ],
            $this->formatter->formatException($this->createExceptionMock())
        );
    }

    /**
     * @return InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createRequestMock()
    {
        $request = $this->createMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
        $request
            ->expects($this->any())
            ->method('getProtocolVersion')
            ->will($this->returnValue('1.1'));

        $request
            ->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue('http://egeloen.fr'));

        $request
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue('GET'));

        $request
            ->expects($this->any())
            ->method('getHeaders')
            ->will($this->returnValue(['foo' => 'bar']));

        $request
            ->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue('foo=bar'));

        $request
            ->expects($this->any())
            ->method('getDatas')
            ->will($this->returnValue(['baz' => 'bat']));

        $request
            ->expects($this->any())
            ->method('getFiles')
            ->will($this->returnValue(['bit' => __FILE__]));

        $request
            ->expects($this->any())
            ->method('getParameters')
            ->will($this->returnValue(['ban' => 'bor']));

        return $request;
    }

    /**
     * @return ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createResponseMock()
    {
        $response = $this->createMock('Ivory\HttpAdapter\Message\ResponseInterface');
        $response
            ->expects($this->any())
            ->method('getProtocolVersion')
            ->will($this->returnValue('1.1'));

        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(200));

        $response
            ->expects($this->any())
            ->method('getReasonPhrase')
            ->will($this->returnValue('OK'));

        $response
            ->expects($this->any())
            ->method('getHeaders')
            ->will($this->returnValue(['bal' => 'bol']));

        $response
            ->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue('body'));

        $response
            ->expects($this->any())
            ->method('getParameters')
            ->will($this->returnValue(['bil' => 'bob']));

        return $response;
    }

    /**
     * @return HttpAdapterException|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createExceptionMock()
    {
        $exception = $this->createMock('Ivory\HttpAdapter\HttpAdapterException');

        $this->setPropertyValue($exception, 'code', 123);
        $this->setPropertyValue($exception, 'message', 'message');
        $this->setPropertyValue($exception, 'line', 234);
        $this->setPropertyValue($exception, 'file', __FILE__);

        return $exception;
    }

    /**
     * @param object $object
     * @param string $property
     * @param mixed  $value
     */
    private function setPropertyValue($object, $property, $value)
    {
        $reflectionProperty = new \ReflectionProperty($object, $property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }
}
