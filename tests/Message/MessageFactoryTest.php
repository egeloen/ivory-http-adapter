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
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class MessageFactoryTest extends AbstractTestCase
{
    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->messageFactory = new MessageFactory();
    }

    public function testInitialState()
    {
        $this->assertFalse($this->messageFactory->hasBaseUri());
    }

    public function testInheritance()
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\MessageFactoryInterface', $this->messageFactory);
    }

    public function testCreateRequest()
    {
        $request = $this->messageFactory->createRequest($uri = 'http://egeloen.fr/');

        $this->assertInstanceOf('Ivory\HttpAdapter\Message\Request', $request);
        $this->assertSame($uri, (string) $request->getUri());
        $this->assertSame(RequestInterface::METHOD_GET, $request->getMethod());
        $this->assertSame(['Host' => ['egeloen.fr']], $request->getHeaders());
        $this->assertEmpty((string) $request->getBody());
        $this->assertEmpty($request->getParameters());
    }

    public function testCreateRequestWithFullInformations()
    {
        $request = $this->messageFactory->createRequest(
            $uri = 'http://egeloen.fr/',
            $method = RequestInterface::METHOD_POST,
            $protocolVersion = RequestInterface::PROTOCOL_VERSION_1_0,
            $headers = ['foo' => ['bar']],
            $body = $this->createMock('Psr\Http\Message\StreamInterface'),
            $parameters = ['baz' => 'bat']
        );

        $headers['Host'] = ['egeloen.fr'];

        $this->assertSame($uri, (string) $request->getUri());
        $this->assertSame($method, $request->getMethod());
        $this->assertSame($protocolVersion, $request->getProtocolVersion());
        $this->assertSame($headers, $request->getHeaders());
        $this->assertSame($body, $request->getBody());
        $this->assertSame($parameters, $request->getParameters());
    }

    public function testCreateInternalRequest()
    {
        $internalRequest = $this->messageFactory->createInternalRequest($uri = 'http://egeloen.fr/');

        $this->assertInstanceOf('Ivory\HttpAdapter\Message\InternalRequest', $internalRequest);
        $this->assertSame($uri, (string) $internalRequest->getUri());
        $this->assertSame(RequestInterface::METHOD_GET, $internalRequest->getMethod());
        $this->assertSame(RequestInterface::PROTOCOL_VERSION_1_1, $internalRequest->getProtocolVersion());
        $this->assertSame(['Host' => ['egeloen.fr']], $internalRequest->getHeaders());
        $this->assertEmpty((string) $internalRequest->getBody());
        $this->assertEmpty($internalRequest->getDatas());
        $this->assertEmpty($internalRequest->getFiles());
        $this->assertEmpty($internalRequest->getParameters());
    }

    public function testCreateInternalRequestWithArrayDatas()
    {
        $internalRequest = $this->messageFactory->createInternalRequest(
            $uri = 'http://egeloen.fr/',
            $method = RequestInterface::METHOD_POST,
            $protocolVersion = RequestInterface::PROTOCOL_VERSION_1_0,
            $headers = ['foo' => ['bar']],
            $datas = ['baz' => 'bat'],
            $files = ['bot' => 'ban'],
            $parameters = ['bip' => 'pog']
        );

        $headers['Host'] = ['egeloen.fr'];

        $this->assertSame($uri, (string) $internalRequest->getUri());
        $this->assertSame($method, $internalRequest->getMethod());
        $this->assertSame($protocolVersion, $internalRequest->getProtocolVersion());
        $this->assertSame($headers, $internalRequest->getHeaders());
        $this->assertEmpty((string) $internalRequest->getBody());
        $this->assertSame($datas, $internalRequest->getDatas());
        $this->assertSame($files, $internalRequest->getFiles());
        $this->assertSame($parameters, $internalRequest->getParameters());
    }

    public function testCreateInternalRequestWithStringDatas()
    {
        $internalRequest = $this->messageFactory->createInternalRequest(
            $uri = 'http://egeloen.fr/',
            $method = RequestInterface::METHOD_POST,
            $protocolVersion = RequestInterface::PROTOCOL_VERSION_1_0,
            $headers = ['foo' => ['bar']],
            $datas = 'baz=bat',
            [],
            $parameters = ['bip' => 'pog']
        );

        $headers['Host'] = ['egeloen.fr'];

        $this->assertSame($uri, (string) $internalRequest->getUri());
        $this->assertSame($method, $internalRequest->getMethod());
        $this->assertSame($protocolVersion, $internalRequest->getProtocolVersion());
        $this->assertSame($headers, $internalRequest->getHeaders());
        $this->assertSame($datas, (string) $internalRequest->getBody());
        $this->assertEmpty($internalRequest->getDatas());
        $this->assertEmpty($internalRequest->getFiles());
        $this->assertSame($parameters, $internalRequest->getParameters());
    }

    public function testCreateInternalRequestWithResourceDatas()
    {
        $resource = fopen('php://memory', 'rw');
        fwrite($resource, $datas = 'baz=bat');

        $internalRequest = $this->messageFactory->createInternalRequest(
            $uri = 'http://egeloen.fr/',
            $method = RequestInterface::METHOD_POST,
            $protocolVersion = RequestInterface::PROTOCOL_VERSION_1_0,
            $headers = ['foo' => ['bar']],
            $resource,
            [],
            $parameters = ['bip' => 'pog']
        );

        $headers['Host'] = ['egeloen.fr'];

        $this->assertSame($uri, (string) $internalRequest->getUri());
        $this->assertSame($method, $internalRequest->getMethod());
        $this->assertSame($protocolVersion, $internalRequest->getProtocolVersion());
        $this->assertSame($headers, $internalRequest->getHeaders());
        $this->assertSame($datas, (string) $internalRequest->getBody());
        $this->assertEmpty($internalRequest->getDatas());
        $this->assertEmpty($internalRequest->getFiles());
        $this->assertSame($parameters, $internalRequest->getParameters());
    }

    public function testCreateResponse()
    {
        $response = $this->messageFactory->createResponse();

        $this->assertInstanceOf('Ivory\HttpAdapter\Message\Response', $response);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('OK', $response->getReasonPhrase());
        $this->assertEmpty($response->getHeaders());
        $this->assertEmpty((string) $response->getBody());
        $this->assertEmpty($response->getParameters());
    }

    public function testCreateResponseWithFullInformations()
    {
        $response = $this->messageFactory->createResponse(
            $statusCode = 404,
            $protocolVersion = RequestInterface::PROTOCOL_VERSION_1_0,
            $headers = ['foo' => ['bar']],
            $body = $this->createMock('Psr\Http\Message\StreamInterface'),
            $parameters = ['baz' => 'bat']
        );

        $this->assertSame($protocolVersion, $response->getProtocolVersion());
        $this->assertSame($statusCode, $response->getStatusCode());
        $this->assertSame($headers, $response->getHeaders());
        $this->assertSame($body, $response->getBody());
        $this->assertSame($parameters, $response->getParameters());
    }

    public function testSetBaseUri()
    {
        $this->messageFactory->setBaseUri($baseUri = 'http://egeloen.fr/');

        $this->assertTrue($this->messageFactory->hasBaseUri());
        $this->assertSame($baseUri, (string) $this->messageFactory->getBaseUri());
    }

    public function testCreateRequestWithBaseUri()
    {
        $this->messageFactory->setBaseUri($baseUri = 'http://egeloen.fr/');

        $request = $this->messageFactory->createRequest($uri = 'test');

        $this->assertInstanceOf('Ivory\HttpAdapter\Message\Request', $request);
        $this->assertSame($baseUri.$uri, (string) $request->getUri());
    }

    public function testCreateInternalRequestWithBaseUri()
    {
        $this->messageFactory->setBaseUri($baseUri = 'http://egeloen.fr/');

        $request = $this->messageFactory->createInternalRequest($uri = 'test');

        $this->assertInstanceOf('Ivory\HttpAdapter\Message\InternalRequest', $request);
        $this->assertSame($baseUri.$uri, (string) $request->getUri());
    }
}
