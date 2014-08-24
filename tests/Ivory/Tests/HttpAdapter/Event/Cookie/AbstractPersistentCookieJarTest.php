<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\Cookie;

use Ivory\HttpAdapter\Event\Cookie\CookieInterface;

/**
 * Abstract persistent cookie jar test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractPersistentCookieJarTest extends AbstractCookieJarTest
{
    /** @var \Ivory\HttpAdapter\Event\Cookie\CookieInterface */
    protected $cookie;

    /** @var \Ivory\HttpAdapter\Event\Cookie\CookieInterface */
    protected $expiredCookie;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->cookie = $this->createCookieMock();
        $this->expiredCookie = $this->createExpiredCookieMock();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->cookie);
        unset($this->expiredCookie);
    }

    /**
     * Gets the cookies.
     *
     * @return array The cookies.
     */
    protected function getCookies()
    {
        return array(
            $this->cookie,
            $this->expiredCookie,
        );
    }

    /**
     * Gets the serialized.
     *
     * @return string The serialized.
     */
    protected function getSerialized()
    {
        return json_encode(array($this->cookie->toArray()));
    }

    /**
     * {@inheritdoc}
     */
    protected function createCookieMock()
    {
        $cookie = parent::createCookieMock();
        $cookie
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('name'));

        $cookie
            ->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue('value'));

        $cookie
            ->expects($this->any())
            ->method('getAttributes')
            ->will($this->returnValue(array(
                CookieInterface::ATTR_DOMAIN  => 'egeloen.fr',
                CookieInterface::ATTR_PATH    => '/',
                CookieInterface::ATTR_SECURE  => false,
                CookieInterface::ATTR_EXPIRES => date('D, d M Y H:i:s e', time() + 100),
                CookieInterface::ATTR_MAX_AGE => 100,
            )));

        $cookie
            ->expects($this->any())
            ->method('getCreatedAt')
            ->will($this->returnValue(time()));

        $cookie
            ->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue(array(
                'name'       => $cookie->getName(),
                'value'      => $cookie->getValue(),
                'attributes' => $cookie->getAttributes(),
                'created_at' => $cookie->getCreatedAt(),
            )));

        return $cookie;
    }

    /**
     * Asserts serialize.
     *
     * @param string $serialized The serialized.
     */
    protected function assertSerialize($serialized)
    {
        $this->assertSame($this->getSerialized(), $serialized);
    }

    /**
     * Asserts the cookies.
     *
     * @param array $cookies The cookies.
     */
    protected function assertCookies(array $cookies)
    {
        $this->assertCount(1, $cookies);
        $this->assertArrayHasKey(0, $cookies);

        $this->assertSame($this->cookie->getName(), $cookies[0]->getName());
        $this->assertSame($this->cookie->getValue(), $cookies[0]->getValue());
        $this->assertSame($this->cookie->getAttributes(), $cookies[0]->getAttributes());
        $this->assertSame($this->cookie->getCreatedAt(), $cookies[0]->getCreatedAt());
    }
}
