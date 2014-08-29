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

use Ivory\HttpAdapter\Event\Cookie\Jar\SessionCookieJar;

/**
 * Session cookie jar test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class SessionCookieJarTest extends AbstractPersistentCookieJarTest
{
    /** @var \Ivory\HttpAdapter\Event\Cookie\Jar\SessionCookieJar */
    protected $sessionCookieJar;

    /** @var string */
    protected $key;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->sessionCookieJar = new SessionCookieJar($this->key = 'foo');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        unset($this->key);
        unset($this->sessionCookieJar);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Cookie\Jar\AbstractPersistentCookieJar',
            $this->sessionCookieJar
        );

        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Cookie\CookieFactory',
            $this->sessionCookieJar->getCookieFactory()
        );

        $this->assertFalse($this->sessionCookieJar->hasCookies());
        $this->assertSame($this->key, $this->sessionCookieJar->getKey());
    }

    public function testInitialState()
    {
        $this->sessionCookieJar = new SessionCookieJar($this->key, $cookieFactory = $this->createCookieFactoryMock());

        $this->assertSame($cookieFactory, $this->sessionCookieJar->getCookieFactory());
    }

    public function testSetKey()
    {
        $this->sessionCookieJar->setKey($key = 'bar');

        $this->assertSame($key, $this->sessionCookieJar->getKey());
    }

    public function testLoad()
    {
        $_SESSION[$this->key] = $this->getSerialized();
        $this->sessionCookieJar->load();

        $this->assertCookies($this->sessionCookieJar->getCookies());
    }

    public function testAutoLoad()
    {
        $_SESSION[$this->key] = $this->getSerialized();
        $this->sessionCookieJar = new SessionCookieJar($this->key);

        $this->assertCookies($this->sessionCookieJar->getCookies());
    }

    public function testSave()
    {
        $this->sessionCookieJar->setCookies($this->cookies);
        $this->sessionCookieJar->save();

        $this->assertSerialize($_SESSION[$this->key]);
    }

    public function testAutoSave()
    {
        $this->sessionCookieJar->setCookies($this->cookies);
        unset($this->sessionCookieJar);

        $this->assertSerialize($_SESSION[$this->key]);
    }
}
