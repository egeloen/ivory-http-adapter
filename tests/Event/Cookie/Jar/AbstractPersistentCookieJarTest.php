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

use Ivory\HttpAdapter\Event\Cookie\CookieInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractPersistentCookieJarTest extends AbstractCookieJarTest
{
    /**
     * @var array
     */
    protected $cookies;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->cookies = [$this->createNamedCookieMock('foo'), $this->createNamedCookieMock('bar')];
    }

    /**
     * @return string
     */
    protected function getSerialized()
    {
        return json_encode(array_map(function (CookieInterface $cookie) {
            return $cookie->toArray();
        }, $this->cookies));
    }

    /**
     * @param string $serialized
     */
    protected function assertSerialize($serialized)
    {
        $this->assertSame($this->getSerialized(), $serialized);
    }

    /**
     * @param array $cookies
     */
    protected function assertCookies(array $cookies)
    {
        $this->assertCount(2, $cookies);

        foreach ([0, 1] as $index) {
            $this->assertArrayHasKey($index, $cookies);
            $this->assertSame($this->cookies[$index]->getName(), $cookies[$index]->getName());
            $this->assertSame($this->cookies[$index]->getValue(), $cookies[$index]->getValue());
            $this->assertSame($this->cookies[$index]->getAttributes(), $cookies[$index]->getAttributes());
            $this->assertSame($this->cookies[$index]->getCreatedAt(), $cookies[$index]->getCreatedAt());
        }
    }

    /**
     * @param string $name
     *
     * @return CookieInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createNamedCookieMock($name)
    {
        $cookie = parent::createCookieMock();
        $cookie
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        $cookie
            ->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue('value'));

        $cookie
            ->expects($this->any())
            ->method('getAttributes')
            ->will($this->returnValue([
                CookieInterface::ATTR_DOMAIN  => 'egeloen.fr',
                CookieInterface::ATTR_PATH    => '/',
                CookieInterface::ATTR_SECURE  => false,
                CookieInterface::ATTR_EXPIRES => date('D, d M Y H:i:s e', time() + 100),
                CookieInterface::ATTR_MAX_AGE => 100,
            ]));

        $cookie
            ->expects($this->any())
            ->method('getCreatedAt')
            ->will($this->returnValue(time()));

        $cookie
            ->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue([
                'name'       => $cookie->getName(),
                'value'      => $cookie->getValue(),
                'attributes' => $cookie->getAttributes(),
                'created_at' => $cookie->getCreatedAt(),
            ]));

        return $cookie;
    }
}
