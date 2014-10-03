<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Message;

use Ivory\HttpAdapter\Message\MessageInterface;
use Psr\Http\Message\StreamableInterface;

/**
 * Message test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\Message\AbstractMessage|\PHPUnit_Framework_MockObject_MockObject */
    protected $message;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->message = $this->getMockForAbstractClass('Ivory\HttpAdapter\Message\AbstractMessage');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->message);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf('Psr\Http\Message\MessageInterface', $this->message);
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\MessageInterface', $this->message);
    }

    public function testDefaultState()
    {
        $this->assertNull($this->message->getProtocolVersion());
        $this->assertNoHeaders();
        $this->assertNoBody();
        $this->assertNoParameters();
    }

    public function testSetProtocolVersion()
    {
        $this->message->setProtocolVersion($protocolVersion = MessageInterface::PROTOCOL_VERSION_1_1);

        $this->assertSame($protocolVersion, $this->message->getProtocolVersion());
    }

    public function testSetBody()
    {
        $this->message->setBody($body = $this->getMock('Psr\Http\Message\StreamableInterface'));

        $this->assertBody($body);
    }

    public function testSetHeadersAsString()
    {
        $this->message->setHeaders(array(
            ' fOo '  => ' bar ,  baz ',
            ' baT '  => ' ban ',
            ' Date ' => ' Fri, 15 aug 2014 12:34:56 UTC ',
        ));

        $this->assertHeaders(array(
            'fOo'  => array('bar', 'baz'),
            'baT'  => array('ban'),
            'Date' => array('Fri, 15 aug 2014 12:34:56 UTC'),
        ));
    }

    public function testSetHeadersAsArray()
    {
        $this->message->setHeaders(array(
            ' fOo '  => array(' bar ', ' baz '),
            ' baT '  => array(' ban '),
            ' Date ' => array(' Fri, 15 aug 2014 12:34:56 UTC '),
        ));

        $this->assertHeaders(array(
            'fOo'  => array('bar', 'baz'),
            'baT'  => array('ban'),
            'Date' => array('Fri, 15 aug 2014 12:34:56 UTC'),
        ));
    }

    public function testSetHeadersWithExistingHeaders()
    {
        $this->message->setHeaders(array(' fOo ' => ' bar '));
        $this->message->setHeaders(array(' foO ' => ' baz '));

        $this->assertHeaders(array('foO' => array('baz')));
    }

    public function testAddHeadersAsString()
    {
        $this->message->setHeaders(array(
            ' fOo '  => ' bar ,  baz ',
            ' Date ' => ' Fri, 14 aug 2014 12:34:56 UTC ',
        ));

        $this->message->addHeaders(array(
            ' foO '  => ' bat ,  ban',
            ' Date ' => ' Fri, 15 aug 2014 12:34:56 UTC ',
            ' Bon '  => ' bin ',
        ));

        $this->assertHeaders(array(
            'foO'  => array('bar', 'baz', 'bat', 'ban'),
            'Date' => array(
                'Fri, 14 aug 2014 12:34:56 UTC',
                'Fri, 15 aug 2014 12:34:56 UTC',
            ),
            'Bon'  => array('bin'),
        ));
    }

    public function testAddHeadersAsArray()
    {
        $this->message->setHeaders(array(
            ' fOo '  => array(' bar ', ' baz '),
            ' Date ' => array(' Fri, 14 aug 2014 12:34:56 UTC '),
        ));

        $this->message->addHeaders(array(
            ' foO '  => array(' bat ', ' ban '),
            ' Date ' => array(' Fri, 15 aug 2014 12:34:56 UTC '),
            ' Bon '  => array(' bin '),
        ));

        $this->assertHeaders(array(
            'foO' => array('bar', 'baz', 'bat', 'ban'),
            'Date' => array(
                'Fri, 14 aug 2014 12:34:56 UTC',
                'Fri, 15 aug 2014 12:34:56 UTC',
            ),
            'Bon' => array('bin'),
        ));
    }

    public function testRemoveHeaders()
    {
        $this->message->setHeaders($headers = array(' fOo ' => 'bar', ' bAz ' => 'bat'));
        $this->message->removeHeaders(array_keys($headers));

        $this->assertNoHeaders();
    }

    public function testSetHeaderAsString()
    {
        $this->message->setHeader(' fOo ', 'bar, baz');

        $this->assertHeaders(array('fOo' => array('bar', 'baz')));
    }

    public function testSetHeaderAsArray()
    {
        $this->message->setHeader(' fOo ', array('bar', 'baz'));

        $this->assertHeaders(array('fOo' => array('bar', 'baz')));
    }

    public function testSetHeaderWithExistingHeader()
    {
        $this->message->setHeader(' fOo ', 'bar');
        $this->message->setHeader(' foO ', 'baz');

        $this->assertHeaders(array('foO' => array('baz')));
    }

    public function testAddHeaderAsString()
    {
        $this->message->setHeader(' fOo ', 'bar, baz');
        $this->message->addHeader(' foO ', 'bat, ban');

        $this->assertHeaders(array('foO' => array('bar', 'baz', 'bat', 'ban')));
    }

    public function testAddHeaderAsArray()
    {
        $this->message->setHeader(' fOo ', array('bar', 'baz'));

        $this->assertHeaders(array('fOo' => array('bar', 'baz')));
    }

    public function testRemoveHeader()
    {
        $this->message->setHeader($header = ' fOo ', 'bar, baz');
        $this->message->removeHeader($header);

        $this->assertNoHeader($header);
    }

    public function testSetParameters()
    {
        $this->message->setParameters($parameters = array('foo' => 'bar'));

        $this->assertParameters($parameters);
    }

    public function testAddParameters()
    {
        $this->message->setParameters(array('foo' => 'bar'));
        $this->message->addParameters(array('foo' => 'baz', 'bat' => 'bot'));

        $this->assertParameters(array('foo' => array('bar', 'baz'), 'bat' => 'bot'));
    }

    public function testRemoveParameters()
    {
        $this->message->setParameters($parameters = array('foo' => 'bar'));
        $this->message->removeParameters(array_keys($parameters));

        $this->assertNoParameters();
    }

    public function testClearParameters()
    {
        $this->message->setParameters(array('foo' => 'bar'));
        $this->message->clearParameters();

        $this->assertNoParameters();
    }

    public function testSetParameter()
    {
        $this->message->setParameter($name = 'foo', $value = 'bar');

        $this->assertParameter($name, $value);
    }

    public function testAddParameter()
    {
        $this->message->setParameter($name = 'foo', $value1 = 'bar');
        $this->message->addParameter($name, $value2 = 'baz');

        $this->assertParameter($name, array($value1, $value2));
    }

    public function testRemoveParameter()
    {
        $this->message->setParameter($name = 'foo', 'bar');
        $this->message->removeParameter($name);

        $this->assertNoParameter($name);
    }

    /**
     * Asserts there are the headers.
     *
     * @param array $headers The headers.
     */
    protected function assertHeaders(array $headers)
    {
        $this->assertTrue($this->message->hasHeaders());
        $this->assertSame($headers, $this->message->getHeaders());

        foreach ($headers as $header => $value) {
            $this->assertHeader($header, $value);
        }
    }

    /**
     * Asserts there are no headers.
     */
    protected function assertNoHeaders()
    {
        $this->assertFalse($this->message->hasHeaders());
        $this->assertEmpty($this->message->getHeaders());
    }

    /**
     * Asserts there is the header.
     *
     * @param string $header The header.
     * @param array  $value  The value.
     */
    protected function assertHeader($header, array $value)
    {
        $this->assertTrue($this->message->hasHeader($header));
        $this->assertSame($value, $this->message->getHeaderAsArray($header));
        $this->assertSame(implode(', ', $value), $this->message->getHeader($header));
    }

    /**
     * Asserts there is no header.
     *
     * @param string $header The header.
     */
    protected function assertNoHeader($header)
    {
        $this->assertFalse($this->message->hasHeader($header));
        $this->assertEmpty($this->message->getHeaderAsArray($header));
        $this->assertSame('', $this->message->getHeader($header));
    }

    /**
     * Asserts there is a body.
     *
     * @param \Ivory\Tests\HttpAdapter\Message\StreamableInterface $body The body.
     */
    protected function assertBody(StreamableInterface $body)
    {
        $this->assertTrue($this->message->hasBody());
        $this->assertSame($body, $this->message->getBody());
    }

    /**
     * Asserts there are no body.
     */
    protected function assertNoBody()
    {
        $this->assertFalse($this->message->hasBody());
        $this->assertNull($this->message->getBody());
    }

    /**
     * Asserts there are the parameters.
     *
     * @param array $parameters The parameters.
     */
    protected function assertParameters(array $parameters)
    {
        $this->assertTrue($this->message->hasParameters());
        $this->assertSame($parameters, $this->message->getParameters());

        foreach ($parameters as $name => $value) {
            $this->assertParameter($name, $value);
        }
    }

    /**
     * Asserts there are no parameters.
     */
    protected function assertNoParameters()
    {
        $this->assertFalse($this->message->hasParameters());
        $this->assertEmpty($this->message->getParameters());
    }

    /**
     * Asserts there is the parameter.
     *
     * @param string $name  The parameter name.
     * @param mixed  $value The parameter value.
     */
    protected function assertParameter($name, $value)
    {
        $this->assertTrue($this->message->hasParameter($name));
        $this->assertSame($value, $this->message->getParameter($name));
    }

    /**
     * Asserts there is no parameter.
     *
     * @param string $name The parameter name.
     */
    protected function assertNoParameter($name)
    {
        $this->assertFalse($this->message->hasParameter($name));
        $this->assertNull($this->message->getParameter($name));
    }
}
