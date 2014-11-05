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

use Ivory\HttpAdapter\Message\MessageFactory;
use Ivory\HttpAdapter\Message\RequestInterface;

/**
 * Message factory test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MessageFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\Message\MessageFactory */
    protected $messageFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->messageFactory = new MessageFactory();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->messageFactory);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\MessageFactoryInterface', $this->messageFactory);
    }

    public function testCreateRequest()
    {
        $request = $this->messageFactory->createRequest($url = 'http://egeloen.fr/');

        $this->assertInstanceOf('Ivory\HttpAdapter\Message\Request', $request);
        $this->assertSame($url, $request->getUrl());
        $this->assertSame(RequestInterface::METHOD_GET, $request->getMethod());

        $this->assertFalse($request->hasHeaders());
        $this->assertEmpty($request->getHeaders());

        $this->assertFalse($request->hasBody());
        $this->assertNull($request->getBody());

        $this->assertFalse($request->hasParameters());
        $this->assertEmpty($request->getParameters());
    }

    public function testCreateRequestWithFullInformations()
    {
        $request = $this->messageFactory->createRequest(
            $url = 'http://egeloen.fr/',
            $method = RequestInterface::METHOD_POST,
            $protocolVersion = RequestInterface::PROTOCOL_VERSION_1_0,
            $headers = array('foo' => array('bar')),
            $body = $this->getMock('Psr\Http\Message\StreamableInterface'),
            $parameters = array('baz' => 'bat')
        );

        $this->assertSame($url, $request->getUrl());
        $this->assertSame($method, $request->getMethod());
        $this->assertSame($protocolVersion, $request->getProtocolVersion());

        $this->assertTrue($request->hasHeaders());
        $this->assertSame($headers, $request->getHeaders());

        $this->assertTrue($request->hasBody());
        $this->assertSame($body, $request->getBody());

        $this->assertTrue($request->hasParameters());
        $this->assertSame($parameters, $request->getParameters());
    }

    public function testCloneRequest()
    {
        $request = $this->messageFactory->createRequest('http://egeloen.fr/');
        $requestClone = $this->messageFactory->cloneRequest($request);

        $this->assertInstanceOf('Ivory\HttpAdapter\Message\Request', $requestClone);
        $this->assertNotSame($requestClone, $request);
    }

    public function testCreateInternalRequest()
    {
        $internalRequest = $this->messageFactory->createInternalRequest($url = 'http://egeloen.fr/');

        $this->assertInstanceOf('Ivory\HttpAdapter\Message\InternalRequest', $internalRequest);
        $this->assertSame($url, $internalRequest->getUrl());
        $this->assertSame(RequestInterface::METHOD_GET, $internalRequest->getMethod());
        $this->assertSame(RequestInterface::PROTOCOL_VERSION_1_1, $internalRequest->getProtocolVersion());

        $this->assertFalse($internalRequest->hasHeaders());
        $this->assertEmpty($internalRequest->getHeaders());

        $this->assertFalse($internalRequest->hasRawDatas());
        $this->assertSame('', $internalRequest->getRawDatas());

        $this->assertFalse($internalRequest->hasDatas());
        $this->assertEmpty($internalRequest->getDatas());

        $this->assertFalse($internalRequest->hasFiles());
        $this->assertEmpty($internalRequest->getFiles());

        $this->assertFalse($internalRequest->hasParameters());
        $this->assertEmpty($internalRequest->getParameters());
    }

    public function testCreateInternalRequestWithFullInformations()
    {
        $internalRequest = $this->messageFactory->createInternalRequest(
            $url = 'http://egeloen.fr/',
            $method = RequestInterface::METHOD_POST,
            $protocolVersion = RequestInterface::PROTOCOL_VERSION_1_0,
            $headers = array('foo' => array('bar')),
            $datas = array('baz' => 'bat'),
            $files = array('bot' => 'ban'),
            $parameters = array('bip' => 'pog')
        );

        $this->assertSame($url, $internalRequest->getUrl());
        $this->assertSame($method, $internalRequest->getMethod());
        $this->assertSame($protocolVersion, $internalRequest->getProtocolVersion());

        $this->assertTrue($internalRequest->hasHeaders());
        $this->assertSame($headers, $internalRequest->getHeaders());

        $this->assertFalse($internalRequest->hasRawDatas());
        $this->assertSame('', $internalRequest->getRawDatas());

        $this->assertTrue($internalRequest->hasDatas());
        $this->assertSame($datas, $internalRequest->getDatas());

        $this->assertTrue($internalRequest->hasFiles());
        $this->assertSame($files, $internalRequest->getFiles());

        $this->assertTrue($internalRequest->hasParameters());
        $this->assertSame($parameters, $internalRequest->getParameters());
    }

    public function testCloneInternalRequest()
    {
        $internalRequest = $this->messageFactory->createInternalRequest('http://egeloen.fr/');
        $internalRequestClone = $this->messageFactory->cloneInternalRequest($internalRequest);

        $this->assertInstanceOf('Ivory\HttpAdapter\Message\InternalRequest', $internalRequestClone);
        $this->assertNotSame($internalRequestClone, $internalRequest);
    }

    public function testCreateResponse()
    {
        $response = $this->messageFactory->createResponse();

        $this->assertInstanceOf('Ivory\HttpAdapter\Message\Response', $response);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('OK', $response->getReasonPhrase());

        $this->assertFalse($response->hasHeaders());
        $this->assertEmpty($response->getHeaders());

        $this->assertFalse($response->hasBody());
        $this->assertNull($response->getBody());

        $this->assertFalse($response->hasParameters());
        $this->assertEmpty($response->getParameters());
    }

    public function testCreateResponseWithFullInformations()
    {
        $response = $this->messageFactory->createResponse(
            $statusCode = 404,
            $reasonPhrase = 'Not Found',
            $protocolVersion = RequestInterface::PROTOCOL_VERSION_1_0,
            $headers = array('foo' => array('bar')),
            $body = $this->getMock('Psr\Http\Message\StreamableInterface'),
            $parameters = array('baz' => 'bat')
        );

        $this->assertSame($protocolVersion, $response->getProtocolVersion());
        $this->assertSame($statusCode, $response->getStatusCode());
        $this->assertSame($reasonPhrase, $response->getReasonPhrase());

        $this->assertTrue($response->hasHeaders());
        $this->assertSame($headers, $response->getHeaders());

        $this->assertTrue($response->hasBody());
        $this->assertSame($body, $response->getBody());

        $this->assertTrue($response->hasParameters());
        $this->assertSame($parameters, $response->getParameters());
    }

    public function testCloneResponse()
    {
        $response = $this->messageFactory->createResponse();
        $responseClone = $this->messageFactory->cloneResponse($response);

        $this->assertInstanceOf('Ivory\HttpAdapter\Message\Response', $responseClone);
        $this->assertNotSame($responseClone, $response);
    }
}
