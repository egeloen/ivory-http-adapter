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

/**
 * Internal request test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class InternalRequestTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\Message\InternalRequest */
    protected $internalRequest;

    /** @var string */
    protected $url;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->internalRequest = new InternalRequest($this->url = 'http://egeloen.fr/');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->url);
        unset($this->internalRequest);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\Request', $this->internalRequest);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->url, $this->internalRequest->getUrl());
        $this->assertSame(InternalRequest::METHOD_GET, $this->internalRequest->getMethod());
    }

    public function testInitialState()
    {
        $this->internalRequest = new InternalRequest($this->url, $method = InternalRequest::METHOD_POST);

        $this->assertSame($this->url, $this->internalRequest->getUrl());
        $this->assertSame($method, $this->internalRequest->getMethod());
    }

    public function testSetDatasAsString()
    {
        $this->internalRequest->setDatas($datas = $this->getDatasAsString());

        $this->assertTrue($this->internalRequest->hasDatas());
        $this->assertTrue($this->internalRequest->hasStringDatas());
        $this->assertFalse($this->internalRequest->hasArrayDatas());
        $this->assertSame($datas, $this->internalRequest->getDatas());
    }

    public function testSetDatasAsArray()
    {
        $this->internalRequest->setDatas($datas = $this->getDatasAsArray());

        $this->assertTrue($this->internalRequest->hasDatas());
        $this->assertTrue($this->internalRequest->hasArrayDatas());
        $this->assertFalse($this->internalRequest->hasStringDatas());
        $this->assertSame($datas, $this->internalRequest->getDatas());
    }

    public function testSetFiles()
    {
        $this->internalRequest->setFiles($files = $this->getFiles());

        $this->assertTrue($this->internalRequest->hasFiles());
        $this->assertSame($files, $this->internalRequest->getFiles());
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testSetDatasAsStringWithFiles()
    {
        $this->internalRequest->setFiles($this->getFiles());
        $this->internalRequest->setDatas($this->getDatasAsString());
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testSetFilesWithDatasAsString()
    {
        $this->internalRequest->setDatas($this->getDatasAsString());
        $this->internalRequest->setFiles($this->getFiles());
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testHasBody()
    {
        $this->internalRequest->hasBody();
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testGetBody()
    {
        $this->internalRequest->getBody();
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testSetBody()
    {
        $this->internalRequest->setBody();
    }

    /**
     * Gets the datas as array.
     *
     * @return array The datas as array.
     */
    protected function getDatasAsArray()
    {
        return array('foo' => 'bar');
    }

    /**
     * Gets the datas as string.
     *
     * @return string The datas as string.
     */
    protected function getDatasAsString()
    {
        return http_build_query($this->getDatasAsArray());
    }

    /**
     * Gets the files.
     *
     * @return array The files.
     */
    protected function getFiles()
    {
        return array(
            'file1' => realpath(__DIR__.'/../Fixtures/files/file1.txt'),
            'file2' => realpath(__DIR__.'/../Fixtures/files/file2.txt'),
        );
    }
}
