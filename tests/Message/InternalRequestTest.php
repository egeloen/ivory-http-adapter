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
        $this->assertSame(InternalRequest::PROTOCOL_VERSION_1_1, $this->internalRequest->getProtocolVersion());

        $this->assertFalse($this->internalRequest->hasHeaders());
        $this->assertEmpty($this->internalRequest->getHeaders());

        $this->assertFalse($this->internalRequest->hasRawDatas());
        $this->assertSame('', $this->internalRequest->getRawDatas());

        $this->assertFalse($this->internalRequest->hasDatas());
        $this->assertEmpty($this->internalRequest->getDatas());

        $this->assertFalse($this->internalRequest->hasFiles());
        $this->assertEmpty($this->internalRequest->getFiles());

        $this->assertFalse($this->internalRequest->hasParameters());
        $this->assertEmpty($this->internalRequest->getParameters());
    }

    public function testInitialStateWithArrayBody()
    {
        $this->internalRequest = new InternalRequest(
            $this->url,
            $method = InternalRequest::METHOD_POST,
            $protocolVersion = InternalRequest::PROTOCOL_VERSION_1_0,
            $headers = array('foo' => array('bar')),
            $datas = array('baz' => 'bat'),
            $files = array('bot' => 'ban'),
            $parameters = array('bip' => 'pog')
        );

        $this->assertSame($this->url, $this->internalRequest->getUrl());
        $this->assertSame($method, $this->internalRequest->getMethod());
        $this->assertSame($protocolVersion, $this->internalRequest->getProtocolVersion());

        $this->assertTrue($this->internalRequest->hasHeaders());
        $this->assertSame($headers, $this->internalRequest->getHeaders());

        $this->assertFalse($this->internalRequest->hasRawDatas());
        $this->assertSame('', $this->internalRequest->getRawDatas());

        $this->assertTrue($this->internalRequest->hasDatas());
        $this->assertSame($datas, $this->internalRequest->getDatas());

        $this->assertTrue($this->internalRequest->hasFiles());
        $this->assertSame($files, $this->internalRequest->getFiles());

        $this->assertTrue($this->internalRequest->hasParameters());
        $this->assertSame($parameters, $this->internalRequest->getParameters());
    }

    public function testInitialStateWithStringBody()
    {
        $this->internalRequest = new InternalRequest(
            $this->url,
            $method = InternalRequest::METHOD_POST,
            $protocolVersion = InternalRequest::PROTOCOL_VERSION_1_0,
            $headers = array('foo' => array('bar')),
            $datas = 'baz',
            array(),
            $parameters = array('bip' => 'pog')
        );

        $this->assertSame($this->url, $this->internalRequest->getUrl());
        $this->assertSame($method, $this->internalRequest->getMethod());
        $this->assertSame($protocolVersion, $this->internalRequest->getProtocolVersion());

        $this->assertTrue($this->internalRequest->hasHeaders());
        $this->assertSame($headers, $this->internalRequest->getHeaders());

        $this->assertTrue($this->internalRequest->hasRawDatas());
        $this->assertSame($datas, $this->internalRequest->getRawDatas());

        $this->assertFalse($this->internalRequest->hasDatas());
        $this->assertEmpty($this->internalRequest->getDatas());

        $this->assertFalse($this->internalRequest->hasFiles());
        $this->assertEmpty($this->internalRequest->getFiles());

        $this->assertTrue($this->internalRequest->hasParameters());
        $this->assertSame($parameters, $this->internalRequest->getParameters());
    }

    public function testSetRawDatas()
    {
        $this->internalRequest->setRawDatas($rawDatas = $this->getRawDatas());

        $this->assertTrue($this->internalRequest->hasRawDatas());
        $this->assertSame($rawDatas, $this->internalRequest->getRawDatas());
    }

    public function testClearRawDatas()
    {
        $this->internalRequest->setRawDatas($this->getRawDatas());
        $this->internalRequest->clearRawDatas();

        $this->assertFalse($this->internalRequest->hasRawDatas());
        $this->assertSame('', $this->internalRequest->getRawDatas());
    }

    public function testSetDatas()
    {
        $this->internalRequest->setDatas($datas = $this->getDatas());

        $this->assertTrue($this->internalRequest->hasDatas());
        $this->assertSame($datas, $this->internalRequest->getDatas());
    }

    public function testClearDatas()
    {
        $this->internalRequest->setDatas($this->getDatas());
        $this->internalRequest->clearDatas();

        $this->assertFalse($this->internalRequest->hasDatas());
        $this->assertEmpty($this->internalRequest->getDatas());
    }

    public function testAddDatas()
    {
        $this->internalRequest->setDatas($datas = $this->getDatas());
        $this->internalRequest->addDatas(array('foo' => 'bat', 'baz' => 'bot'));

        $this->assertSame(array('foo' => array('bar', 'bat'), 'baz' => 'bot'), $this->internalRequest->getDatas());
    }

    public function testRemoveDatas()
    {
        $this->internalRequest->setDatas($datas = $this->getDatas());
        $this->internalRequest->removeDatas(array_keys($datas));

        $this->assertFalse($this->internalRequest->hasDatas());
        $this->assertEmpty($this->internalRequest->getDatas());
    }

    public function testSetData()
    {
        $this->internalRequest->setDatas($this->getDatas());
        $this->internalRequest->setData($name = 'foo', $value = 'baz');

        $this->assertTrue($this->internalRequest->hasData($name));
        $this->assertSame($value, $this->internalRequest->getData($name));
    }

    public function testAddData()
    {
        $this->internalRequest->setDatas($this->getDatas());
        $this->internalRequest->addData($name = 'foo', $value = 'baz');

        $this->assertTrue($this->internalRequest->hasData($name));
        $this->assertSame(array('bar', $value), $this->internalRequest->getData($name));
    }

    public function testRemoveData()
    {
        $this->internalRequest->addData($name = 'foo', 'bar');
        $this->internalRequest->removeData($name);

        $this->assertFalse($this->internalRequest->hasData($name));
        $this->assertNull($this->internalRequest->getData($name));
    }

    public function testSetFiles()
    {
        $this->internalRequest->setFiles($files = $this->getFiles());

        $this->assertTrue($this->internalRequest->hasFiles());
        $this->assertSame($files, $this->internalRequest->getFiles());
    }

    public function testClearFiles()
    {
        $this->internalRequest->setFiles($this->getFiles());
        $this->internalRequest->clearFiles();

        $this->assertFalse($this->internalRequest->hasFiles());
        $this->assertEmpty($this->internalRequest->getFiles());
    }

    public function testAddFiles()
    {
        $this->internalRequest->setFiles($this->getFiles());
        $this->internalRequest->addFiles(array(
            'file2' => realpath(__DIR__.'/../Fixtures/files/file1.txt'),
            'file3' => realpath(__DIR__.'/../Fixtures/files/file3.txt'),
        ));


        $this->assertSame(
            array(
                'file1' => realpath(__DIR__.'/../Fixtures/files/file1.txt'),
                'file2' => array(
                    realpath(__DIR__.'/../Fixtures/files/file2.txt'),
                    realpath(__DIR__.'/../Fixtures/files/file1.txt'),
                ),
                'file3' => realpath(__DIR__.'/../Fixtures/files/file3.txt'),
            ),
            $this->internalRequest->getFiles()
        );
    }

    public function testRemoveFiles()
    {
        $this->internalRequest->setFiles($files = $this->getFiles());
        $this->internalRequest->removeFiles(array_keys($files));

        $this->assertFalse($this->internalRequest->hasFiles());
        $this->assertEmpty($this->internalRequest->getFiles());
    }

    public function testSetFile()
    {
        $this->internalRequest->setFiles($this->getFiles());
        $this->internalRequest->setFile(
            $name = 'file2',
            realpath(__DIR__.'/../Fixtures/files/file1.txt')
        );

        $this->assertTrue($this->internalRequest->hasFile($name));
        $this->assertSame(realpath(__DIR__.'/../Fixtures/files/file1.txt'), $this->internalRequest->getFile($name));
    }

    public function testAddFile()
    {
        $this->internalRequest->setFiles($this->getFiles());
        $this->internalRequest->addFile($name = 'file2', realpath(__DIR__.'/../Fixtures/files/file1.txt'));

        $this->assertTrue($this->internalRequest->hasFile($name));
        $this->assertSame(
            array(
                realpath(__DIR__.'/../Fixtures/files/file2.txt'),
                realpath(__DIR__.'/../Fixtures/files/file1.txt'),
            ),
            $this->internalRequest->getFile($name)
        );
    }

    public function testRemoveFile()
    {
        $this->internalRequest->setFiles($this->getFiles());
        $this->internalRequest->removeFile($name = 'file1');

        $this->assertFalse($this->internalRequest->hasFile($name));
        $this->assertNull($this->internalRequest->getFile($name));
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testSetRawDatasWithDatas()
    {
        $this->internalRequest->setDatas($this->getDatas());
        $this->internalRequest->setRawDatas($this->getRawDatas());
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testSetRawDatasWithFiles()
    {
        $this->internalRequest->setFiles($this->getFiles());
        $this->internalRequest->setRawDatas($this->getRawDatas());
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testSetDatasWithRawDatas()
    {
        $this->internalRequest->setRawDatas($this->getRawDatas());
        $this->internalRequest->setDatas($this->getDatas());
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testSetDataWithRawDatas()
    {
        $this->internalRequest->setRawDatas($this->getRawDatas());
        $this->internalRequest->setData('foo', 'bar');
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testAddDataWithRawDatas()
    {
        $this->internalRequest->setRawDatas($this->getRawDatas());
        $this->internalRequest->addData('foo', 'bar');
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testSetFilesWithRawDatas()
    {
        $this->internalRequest->setRawDatas($this->getRawDatas());
        $this->internalRequest->setFiles($this->getFiles());
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testSetFileWithRawDatas()
    {
        $this->internalRequest->setRawDatas($this->getRawDatas());
        $this->internalRequest->setFile('file1', realpath(__DIR__.'/../Fixtures/files/file1.txt'));
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testAddFileWithRawDatas()
    {
        $this->internalRequest->setRawDatas($this->getRawDatas());
        $this->internalRequest->addFile('file1', realpath(__DIR__.'/../Fixtures/files/file1.txt'));
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
     * Gets the raw datas.
     *
     * @return string The raw datas.
     */
    protected function getRawDatas()
    {
        return http_build_query($this->getDatas());
    }

    /**
     * Gets the datas.
     *
     * @return array The datas.
     */
    protected function getDatas()
    {
        return array('foo' => 'bar');
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
