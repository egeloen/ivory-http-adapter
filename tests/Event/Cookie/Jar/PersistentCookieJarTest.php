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

use Ivory\HttpAdapter\Event\Cookie\Jar\AbstractPersistentCookieJar;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class PersistentCookieJarTest extends AbstractPersistentCookieJarTest
{
    /**
     * @var AbstractPersistentCookieJar|\PHPUnit_Framework_MockObject_MockObject
     */
    private $persistentCookieJar;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->persistentCookieJar = $this->createPersistentCookieJarMock();
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
            ->setConstructorArgs([$cookieFactory = $this->createCookieFactoryMock(), false])
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
     * @return AbstractPersistentCookieJar|\PHPUnit_Framework_MockObject_MockBuilder
     */
    private function createPersistentCookieJarMockBuilder()
    {
        return $this->getMockBuilder('Ivory\HttpAdapter\Event\Cookie\Jar\AbstractPersistentCookieJar');
    }

    /**
     * @return AbstractPersistentCookieJar|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createPersistentCookieJarMock()
    {
        return $this->createPersistentCookieJarMockBuilder()->getMockForAbstractClass();
    }
}
