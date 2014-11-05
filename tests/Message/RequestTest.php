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

use Ivory\HttpAdapter\Message\Request;
use Ivory\Tests\HttpAdapter\Normalizer\AbstractUrlNormalizerTest;
use Psr\Http\Message\StreamableInterface;

/**
 * Request test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class RequestTest extends AbstractUrlNormalizerTest
{
    /** @var \Ivory\HttpAdapter\Message\Request */
    protected $request;

    /** @var string */
    protected $url;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->request = new Request($this->url = 'http://egeloen.fr/');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->url);
        unset($this->request);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf('Psr\Http\Message\OutgoingRequestInterface', $this->request);
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\RequestInterface', $this->request);
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\AbstractMessage', $this->request);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->url, $this->request->getUrl());
        $this->assertSame(Request::METHOD_GET, $this->request->getMethod());
        $this->assertSame(Request::PROTOCOL_VERSION_1_1, $this->request->getProtocolVersion());
        $this->assertNoHeaders();
        $this->assertNoBody();
        $this->assertNoParameters();
    }

    public function testInitialState()
    {
        $this->request = new Request(
            $this->url,
            $method = Request::METHOD_POST,
            $protocolVersion = Request::PROTOCOL_VERSION_1_0,
            $headers = array('foo' => array('bar')),
            $body = $this->getMock('Psr\Http\Message\StreamableInterface'),
            $parameters = array('baz' => 'bat')
        );

        $this->assertSame($method, $this->request->getMethod());
        $this->assertSame($protocolVersion, $this->request->getProtocolVersion());
        $this->assertHeaders($headers);
        $this->assertBody($body);
        $this->assertParameters($parameters);
    }

    /**
     * @dataProvider validUrlProvider
     */
    public function testSetUrlWithValidUrl($url)
    {
        $this->request->setUrl($url);

        $this->assertSame($url, $this->request->getUrl());
    }

    /**
     * @dataProvider invalidUrlProvider
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testSetUrlWithInvalidUrl($url)
    {
        $this->request->setUrl($url);
    }

    public function testSetMethod()
    {
        $this->request->setMethod($method = Request::METHOD_POST);

        $this->assertSame($method, $this->request->getMethod());
    }

    public function testSetMethodLowercase()
    {
        $this->request->setMethod('post');

        $this->assertSame(Request::METHOD_POST, $this->request->getMethod());
    }

    public function testSetProtocolVersion()
    {
        $this->request->setProtocolVersion($protocolVersion = Request::PROTOCOL_VERSION_1_1);

        $this->assertSame($protocolVersion, $this->request->getProtocolVersion());
    }

    public function testSetBody()
    {
        $this->request->setBody($body = $this->getMock('Psr\Http\Message\StreamableInterface'));

        $this->assertBody($body);
    }

    public function testSetHeadersAsString()
    {
        $this->request->setHeaders(array(
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
        $this->request->setHeaders(array(
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
        $this->request->setHeaders(array(' fOo ' => ' bar '));
        $this->request->setHeaders(array(' foO ' => ' baz '));

        $this->assertHeaders(array('foO' => array('baz')));
    }

    public function testAddHeadersAsString()
    {
        $this->request->setHeaders(array(
            ' fOo '  => ' bar ,  baz ',
            ' Date ' => ' Fri, 14 aug 2014 12:34:56 UTC ',
        ));

        $this->request->addHeaders(array(
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
        $this->request->setHeaders(array(
            ' fOo '  => array(' bar ', ' baz '),
            ' Date ' => array(' Fri, 14 aug 2014 12:34:56 UTC '),
        ));

        $this->request->addHeaders(array(
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
        $this->request->setHeaders($headers = array(' fOo ' => 'bar', ' bAz ' => 'bat'));
        $this->request->removeHeaders(array_keys($headers));

        $this->assertNoHeaders();
    }

    public function testSetHeaderAsString()
    {
        $this->request->setHeader(' fOo ', 'bar, baz');

        $this->assertHeaders(array('fOo' => array('bar', 'baz')));
    }

    public function testSetHeaderAsArray()
    {
        $this->request->setHeader(' fOo ', array('bar', 'baz'));

        $this->assertHeaders(array('fOo' => array('bar', 'baz')));
    }

    public function testSetHeaderWithExistingHeader()
    {
        $this->request->setHeader(' fOo ', 'bar');
        $this->request->setHeader(' foO ', 'baz');

        $this->assertHeaders(array('foO' => array('baz')));
    }

    public function testAddHeaderAsString()
    {
        $this->request->setHeader(' fOo ', 'bar, baz');
        $this->request->addHeader(' foO ', 'bat, ban');

        $this->assertHeaders(array('foO' => array('bar', 'baz', 'bat', 'ban')));
    }

    public function testAddHeaderAsArray()
    {
        $this->request->setHeader(' fOo ', array('bar', 'baz'));

        $this->assertHeaders(array('fOo' => array('bar', 'baz')));
    }

    public function testRemoveHeader()
    {
        $this->request->setHeader($header = ' fOo ', 'bar, baz');
        $this->request->removeHeader($header);

        $this->assertNoHeader($header);
    }

    /**
     * Asserts there are the headers.
     *
     * @param array $headers The headers.
     */
    protected function assertHeaders(array $headers)
    {
        $this->assertTrue($this->request->hasHeaders());
        $this->assertSame($headers, $this->request->getHeaders());

        foreach ($headers as $header => $value) {
            $this->assertHeader($header, $value);
        }
    }

    /**
     * Asserts there are no headers.
     */
    protected function assertNoHeaders()
    {
        $this->assertFalse($this->request->hasHeaders());
        $this->assertEmpty($this->request->getHeaders());
    }

    /**
     * Asserts there is the header.
     *
     * @param string $header The header.
     * @param array  $value  The value.
     */
    protected function assertHeader($header, array $value)
    {
        $this->assertTrue($this->request->hasHeader($header));
        $this->assertSame($value, $this->request->getHeaderAsArray($header));
        $this->assertSame(implode(', ', $value), $this->request->getHeader($header));
    }

    /**
     * Asserts there is no header.
     *
     * @param string $header The header.
     */
    protected function assertNoHeader($header)
    {
        $this->assertFalse($this->request->hasHeader($header));
        $this->assertEmpty($this->request->getHeaderAsArray($header));
        $this->assertSame('', $this->request->getHeader($header));
    }

    /**
     * Asserts there is a body.
     *
     * @param \Ivory\Tests\HttpAdapter\Message\StreamableInterface $body The body.
     */
    protected function assertBody(StreamableInterface $body)
    {
        $this->assertTrue($this->request->hasBody());
        $this->assertSame($body, $this->request->getBody());
    }

    /**
     * Asserts there are no body.
     */
    protected function assertNoBody()
    {
        $this->assertFalse($this->request->hasBody());
        $this->assertNull($this->request->getBody());
    }

    /**
     * Asserts there are the parameters.
     *
     * @param array $parameters The parameters.
     */
    protected function assertParameters(array $parameters)
    {
        $this->assertTrue($this->request->hasParameters());
        $this->assertSame($parameters, $this->request->getParameters());

        foreach ($parameters as $name => $value) {
            $this->assertParameter($name, $value);
        }
    }

    /**
     * Asserts there are no parameters.
     */
    protected function assertNoParameters()
    {
        $this->assertFalse($this->request->hasParameters());
        $this->assertEmpty($this->request->getParameters());
    }

    /**
     * Asserts there is the parameter.
     *
     * @param string $name  The parameter name.
     * @param mixed  $value The parameter value.
     */
    protected function assertParameter($name, $value)
    {
        $this->assertTrue($this->request->hasParameter($name));
        $this->assertSame($value, $this->request->getParameter($name));
    }
}
