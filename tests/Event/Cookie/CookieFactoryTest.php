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

use Ivory\HttpAdapter\Event\Cookie\CookieFactory;
use Ivory\HttpAdapter\Event\Cookie\CookieInterface;
use Ivory\Tests\HttpAdapter\Parser\AbstractCookieParserTest;

/**
 * Cookie factory test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CookieFactoryTest extends AbstractCookieParserTest
{
    /** @var \Ivory\HttpAdapter\Event\Cookie\CookieFactory */
    protected $cookieFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->cookieFactory = new CookieFactory();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->cookieFactory);
    }

    public function testCreate()
    {
        $cookie = $this->cookieFactory->create(
            $name = 'foo',
            $value = 'bar',
            $attributes = array(
                CookieInterface::ATTR_DOMAIN  => 'egeloen.fr',
                CookieInterface::ATTR_PATH    => '/',
                CookieInterface::ATTR_SECURE  => false,
                CookieInterface::ATTR_EXPIRES => date('D, d M Y H:i:s e', time() + 100),
                CookieInterface::ATTR_MAX_AGE => 100,
            ),
            $createdAt = time()
        );

        $this->assertInstanceOf('Ivory\HttpAdapter\Event\Cookie\Cookie', $cookie);
        $this->assertSame($name, $cookie->getName());
        $this->assertSame($value, $cookie->getValue());
        $this->assertSame($attributes, $cookie->getAttributes());
        $this->assertSame($createdAt, $cookie->getCreatedAt());
    }

    /**
     * @dataProvider parseProvider
     */
    public function testParse($header, $name, $value, array $attributes = array())
    {
        $before = time();
        $cookie = $this->cookieFactory->parse($header);
        $after = time();

        $this->assertInstanceOf('Ivory\HttpAdapter\Event\Cookie\Cookie', $cookie);
        $this->assertSame($name, $cookie->getName());
        $this->assertSame($value, $cookie->getValue());
        $this->assertSame($attributes, $cookie->getAttributes());
        $this->assertGreaterThanOrEqual($before, $cookie->getCreatedAt());
        $this->assertLessThanOrEqual($after, $cookie->getCreatedAt());
    }
}
