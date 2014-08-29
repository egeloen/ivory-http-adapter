<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\Cookie\Jar;

use Ivory\HttpAdapter\Event\Cookie\Jar\FileCookieJar;
use Ivory\Tests\HttpAdapter\Utility\PHPUnitUtility;

/**
 * File cookie jar test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class FileCookieJarTest extends AbstractPersistentCookieJarTest
{
    /** @var \Ivory\HttpAdapter\Event\Cookie\Jar\FileCookieJar */
    protected $fileCookieJar;

    /** @var string */
    protected $file;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->fileCookieJar = new FileCookieJar($this->file = PHPUnitUtility::getFile());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->fileCookieJar);

        if (file_exists($this->file)) {
            unlink($this->file);
        }

        unset($this->file);

        parent::tearDown();
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Event\Cookie\Jar\AbstractPersistentCookieJar', $this->fileCookieJar);

        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Cookie\CookieFactory',
            $this->fileCookieJar->getCookieFactory()
        );

        $this->assertFalse($this->fileCookieJar->hasCookies());
        $this->assertSame($this->file, $this->fileCookieJar->getFile());
    }

    public function testInitialState()
    {
        $this->fileCookieJar = new FileCookieJar($this->file, $cookieFactory = $this->createCookieFactoryMock());

        $this->assertSame($cookieFactory, $this->fileCookieJar->getCookieFactory());
    }

    public function testSetFile()
    {
        $this->fileCookieJar->setFile($this->file = PHPUnitUtility::getFile());

        $this->assertSame($this->file, $this->fileCookieJar->getFile());
    }

    public function testLoad()
    {
        file_put_contents($this->file, $this->getSerialized());
        $this->fileCookieJar->load();

        $this->assertCookies($this->fileCookieJar->getCookies());
    }

    public function testAutoLoad()
    {
        file_put_contents($this->file, $this->getSerialized());
        $this->fileCookieJar = new FileCookieJar($this->file);

        $this->assertCookies($this->fileCookieJar->getCookies());
    }

    public function testSave()
    {
        $this->fileCookieJar->setCookies($this->cookies);
        $this->fileCookieJar->save();

        $this->assertSerialize(file_get_contents($this->file));
    }

    public function testAutoSave()
    {
        $this->fileCookieJar->setCookies($this->cookies);
        unset($this->fileCookieJar);

        $this->assertSerialize(file_get_contents($this->file));
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testLoadWithNonExistingFile()
    {
        $this->fileCookieJar->load();
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testSaveWithUnwritableFile()
    {
        $this->fileCookieJar->setFile(PHPUnitUtility::getFile(false));
        unset($this->fileCookieJar);
    }
}
