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

/**
 * Persistent cookie jar test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PersistentCookieJarTest extends AbstractPersistentCookieJarTest
{
    /** @var \Ivory\HttpAdapter\Event\Cookie\Jar\AbstractPersistentCookieJar|\PHPUnit_Framework_MockObject_MockObject */
    protected $persistentCookieJar;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->persistentCookieJar = $this->createPersistentCookieJarMock();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        unset($this->persistentCookieJar);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Event\Cookie\Jar\CookieJar', $this->persistentCookieJar);

        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Cookie\Jar\PersistentCookieJarInterface',
            $this->persistentCookieJar
        );

        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Cookie\CookieFactory',
            $this->persistentCookieJar->getCookieFactory()
        );
    }

    public function testInitialState()
    {
        $this->persistentCookieJar = $this->createPersistentCookieJarMockBuilder()
            ->setConstructorArgs(array($cookieFactory = $this->createCookieFactoryMock(), false))
            ->getMockForAbstractClass();

        $this->assertSame($cookieFactory, $this->persistentCookieJar->getCookieFactory());
    }

    public function testSerialize()
    {
        $this->persistentCookieJar->setCookies($this->cookies);

        $this->assertSerialize($this->persistentCookieJar->serialize());
    }

    public function testUnserializeWithValidData()
    {
        $this->persistentCookieJar->setCookies($this->cookies);

        $copy = $this->createPersistentCookieJarMock();
        $copy->unserialize($this->persistentCookieJar->serialize());

        $this->assertCookies($copy->getCookies());
    }

    public function testUnserializeWithInvalidData()
    {
        $this->persistentCookieJar->unserialize('foo');

        $this->assertFalse($this->persistentCookieJar->hasCookies());
    }

    /**
     * Creates a persistent cookie jar mock builder.
     *
     * @return \PHPUnit_Framework_MockObject_MockBuilder The persistent cookie jar mock builder.
     */
    protected function createPersistentCookieJarMockBuilder()
    {
        return $this->getMockBuilder('Ivory\HttpAdapter\Event\Cookie\Jar\AbstractPersistentCookieJar');
    }

    /**
     * Creates a persistent cookie jar mock.
     *
     * @return \Ivory\HttpAdapter\Event\Cookie\Jar\AbstractPersistentCookieJar|\PHPUnit_Framework_MockObject_MockObject The persistent cookie jar mock.
     */
    protected function createPersistentCookieJarMock()
    {
        return $this->createPersistentCookieJarMockBuilder()->getMockForAbstractClass();
    }
}
