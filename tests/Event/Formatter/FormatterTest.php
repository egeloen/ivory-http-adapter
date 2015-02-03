<?php

/**
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\Formatter;

use Ivory\HttpAdapter\Event\Formatter\Formatter;

/**
 * Formatter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class FormatterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\Event\Formatter\Formatter */
    private $formatter;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->formatter = new Formatter();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->formatter);
    }

    public function testFormatRequest()
    {
        $this->assertSame(
            array(
                'protocol_version' => '1.1',
                'url'              => 'http://egeloen.fr',
                'method'           => 'GET',
                'headers'          => array('foo' => 'bar'),
                'raw_datas'        => 'foo=bar',
                'datas'            => array('baz' => 'bat'),
                'files'            => array('bit' => __FILE__),
                'parameters'       => array('ban' => 'bor'),
            ),
            $this->formatter->formatRequest($this->createRequestMock())
        );
    }

    public function testFormatResponse()
    {
        $this->assertSame(
            array(
                'protocol_version' => '1.1',
                'status_code'      => 200,
                'reason_phrase'    => 'OK',
                'headers'          => array('bal' => 'bol'),
                'body'             => 'body',
                'parameters'       => array('bil' => 'bob'),
            ),
            $this->formatter->formatResponse($this->createResponseMock())
        );
    }

    public function testFormatException()
    {
        $this->assertSame(
            array(
                'code' => 123,
                'message' => 'message',
                'line' => 234,
                'file' => __FILE__,
            ),
            $this->formatter->formatException($this->createExceptionMock())
        );
    }

    /**
     * Creates a request mock.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject The request mock.
     */
    private function createRequestMock()
    {
        $request = $this->getMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
        $request
            ->expects($this->any())
            ->method('getProtocolVersion')
            ->will($this->returnValue('1.1'));

        $request
            ->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue('http://egeloen.fr'));

        $request
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue('GET'));

        $request
            ->expects($this->any())
            ->method('getHeaders')
            ->will($this->returnValue(array('foo' => 'bar')));

        $request
            ->expects($this->any())
            ->method('getRawDatas')
            ->will($this->returnValue('foo=bar'));

        $request
            ->expects($this->any())
            ->method('getDatas')
            ->will($this->returnValue(array('baz' => 'bat')));

        $request
            ->expects($this->any())
            ->method('getFiles')
            ->will($this->returnValue(array('bit' => __FILE__)));

        $request
            ->expects($this->any())
            ->method('getParameters')
            ->will($this->returnValue(array('ban' => 'bor')));

        return $request;
    }

    /**
     * Creates a response mock.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject The response interface.
     */
    private function createResponseMock()
    {
        $response = $this->getMock('Ivory\HttpAdapter\Message\ResponseInterface');
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
            ->will($this->returnValue(array('bal' => 'bol')));

        $response
            ->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue('body'));

        $response
            ->expects($this->any())
            ->method('getParameters')
            ->will($this->returnValue(array('bil' => 'bob')));

        return $response;
    }

    /**
     * Creates an exception mock.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException|\PHPUnit_Framework_MockObject_MockObject The exception mock.
     */
    private function createExceptionMock()
    {
        $exception = $this->getMock('Ivory\HttpAdapter\HttpAdapterException');

        $this->setPropertyValue($exception, 'code', 123);
        $this->setPropertyValue($exception, 'message', 'message');
        $this->setPropertyValue($exception, 'line', 234);
        $this->setPropertyValue($exception, 'file', __FILE__);

        return $exception;
    }

    /**
     * Sets a property value.
     *
     * @param object $object   The object.
     * @param string $property The property.
     * @param mixed  $value    The value.
     */
    private function setPropertyValue($object, $property, $value)
    {
        $reflectionProperty = new \ReflectionProperty($object, $property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }
}
