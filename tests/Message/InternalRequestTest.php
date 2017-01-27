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

use Ivory\HttpAdapter\Message\InternalRequest;
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class InternalRequestTest extends AbstractTestCase
{
    /**
     * @var InternalRequest
     */
    private $internalRequest;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->internalRequest = new InternalRequest();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\Request', $this->internalRequest);
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\InternalRequestInterface', $this->internalRequest);
    }

    public function testDefaultState()
    {
        $this->assertEmpty((string) $this->internalRequest->getUri());
        $this->assertEmpty($this->internalRequest->getMethod());
        $this->assertEmpty($this->internalRequest->getHeaders());
        $this->assertEmpty((string) $this->internalRequest->getBody());
        $this->assertEmpty($this->internalRequest->getDatas());
        $this->assertEmpty($this->internalRequest->getFiles());
        $this->assertEmpty($this->internalRequest->getParameters());
    }

    public function testInitialState()
    {
        $this->internalRequest = new InternalRequest(
            $uri = 'http://egeloen.fr/',
            $method = InternalRequest::METHOD_POST,
            'php://memory',
            $datas = ['baz' => 'bat'],
            $files = ['bot' => 'ban'],
            $headers = ['foo' => ['bar']],
            $parameters = ['bip' => 'pog']
        );

        $headers['Host'] = ['egeloen.fr'];

        $this->assertSame($uri, (string) $this->internalRequest->getUri());
        $this->assertSame($method, $this->internalRequest->getMethod());
        $this->assertSame($headers, $this->internalRequest->getHeaders());
        $this->assertEmpty((string) $this->internalRequest->getBody());
        $this->assertSame($datas, $this->internalRequest->getDatas());
        $this->assertSame($files, $this->internalRequest->getFiles());
        $this->assertSame($parameters, $this->internalRequest->getParameters());
    }

    public function testWithData()
    {
        $internalRequest = $this->internalRequest->withData($name = 'foo', 'bar');
        $internalRequest = $internalRequest->withData($name, $value = 'baz');

        $this->assertNotSame($internalRequest, $this->internalRequest);
        $this->assertTrue($internalRequest->hasData($name));
        $this->assertSame($value, $internalRequest->getData($name));
        $this->assertSame([$name => $value], $internalRequest->getDatas());
    }

    public function testWithAddedData()
    {
        $internalRequest = $this->internalRequest->withAddedData($name = 'foo', $value1 = 'bar');
        $internalRequest = $internalRequest->withAddedData($name, $value2 = 'baz');

        $this->assertNotSame($internalRequest, $this->internalRequest);
        $this->assertTrue($internalRequest->hasData($name));
        $this->assertSame([$value1, $value2], $internalRequest->getData($name));
        $this->assertSame([$name => [$value1, $value2]], $internalRequest->getDatas());
    }

    public function testWithoutData()
    {
        $internalRequest = $this->internalRequest->withData($name = 'foo', 'bar');
        $internalRequest = $internalRequest->withoutData($name);

        $this->assertNotSame($internalRequest, $this->internalRequest);
        $this->assertFalse($internalRequest->hasData($name));
        $this->assertNull($internalRequest->getData($name));
        $this->assertEmpty($internalRequest->getDatas());
    }

    public function testWithFile()
    {
        $internalRequest = $this->internalRequest->withFile($name = 'foo', 'bar');
        $internalRequest = $internalRequest->withFile($name, $value = 'baz');

        $this->assertNotSame($internalRequest, $this->internalRequest);
        $this->assertTrue($internalRequest->hasFile($name));
        $this->assertSame($value, $internalRequest->getFile($name));
        $this->assertSame([$name => $value], $internalRequest->getFiles());
    }

    public function testWithAddedFile()
    {
        $internalRequest = $this->internalRequest->withAddedFile($name = 'foo', $value1 = 'bar');
        $internalRequest = $internalRequest->withAddedFile($name, $value2 = 'baz');

        $this->assertNotSame($internalRequest, $this->internalRequest);
        $this->assertTrue($internalRequest->hasFile($name));
        $this->assertSame([$value1, $value2], $internalRequest->getFile($name));
        $this->assertSame([$name => [$value1, $value2]], $internalRequest->getFiles());
    }

    public function testWithoutFile()
    {
        $internalRequest = $this->internalRequest->withFile($name = 'foo', 'bar');
        $internalRequest = $internalRequest->withoutFile($name);

        $this->assertNotSame($internalRequest, $this->internalRequest);
        $this->assertFalse($internalRequest->hasFile($name));
        $this->assertNull($internalRequest->getFile($name));
        $this->assertEmpty($internalRequest->getFiles());
    }
}
