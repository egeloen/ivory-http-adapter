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

use Ivory\HttpAdapter\Event\Cookie\Cookie;
use Ivory\HttpAdapter\Message\InternalRequest;
use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * Cookie test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CookieTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\Event\Cookie\Cookie */
    protected $cookie;

    /** @var string */
    protected $name;

    /** @var string */
    protected $value;

    /** @var array */
    protected $attributes;

    /** @var integer */
    protected $createdAt;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->cookie = new Cookie(
            $this->name = 'name',
            $this->value = 'value',
            $this->attributes = array(
                Cookie::ATTR_DOMAIN  => 'egeloen.fr',
                Cookie::ATTR_PATH    => '/',
                Cookie::ATTR_SECURE  => false,
                Cookie::ATTR_EXPIRES => date('D, d M Y H:i:s e', time() + 100),
                Cookie::ATTR_MAX_AGE => 100,
            ),
            $this->createdAt = time()
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->createdAt);
        unset($this->attributes);
        unset($this->value);
        unset($this->name);
        unset($this->cookie);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->name, $this->cookie->getName());
        $this->assertSame($this->value, $this->cookie->getValue());

        $this->assertTrue($this->cookie->hasAttributes());
        $this->assertSame($this->attributes, $this->cookie->getAttributes());

        foreach ($this->attributes as $name => $value) {
            $this->assertTrue($this->cookie->hasAttribute($name));
            $this->assertSame($value, $this->cookie->getAttribute($name));
        }

        $this->assertSame($this->createdAt, $this->cookie->getCreatedAt());
    }

    public function testInitialState()
    {
        $this->cookie = new Cookie($this->name, $this->value, array(), $this->createdAt);

        $this->assertTrue($this->cookie->hasAttribute(Cookie::ATTR_SECURE));
        $this->assertFalse($this->cookie->getAttribute(Cookie::ATTR_SECURE));
    }

    public function testSetName()
    {
        $this->cookie->setName($name = 'foo');

        $this->assertSame($name, $this->cookie->getName());
    }

    public function testSetValue()
    {
        $this->cookie->setValue($value = 'foo');

        $this->assertSame($value, $this->cookie->getValue());
    }

    public function testSetAttributes()
    {
        $this->cookie->setAttributes($attributes = array(Cookie::ATTR_DOMAIN => 'foo'));

        $this->assertSame($attributes, $this->cookie->getAttributes());
    }

    public function testAddAttributes()
    {
        $this->cookie->addAttributes($attributes = array(Cookie::ATTR_DOMAIN => 'foo'));

        $this->assertSame(array_merge($this->attributes, $attributes), $this->cookie->getAttributes());
    }

    public function testRemveAttributes()
    {
        $this->cookie->removeAttributes(array_keys($this->attributes));

        $this->assertFalse($this->cookie->hasAttributes());
        $this->assertEmpty($this->cookie->getAttributes());
    }

    public function testSetAttribute()
    {
        $this->cookie->setAttribute($name = Cookie::ATTR_DOMAIN, $value = 'foo');

        $this->assertTrue($this->cookie->hasAttribute($name));
        $this->assertSame($value, $this->cookie->getAttribute($name));
    }

    public function testRemoveAttribute()
    {
        $this->cookie->removeAttribute($name = Cookie::ATTR_DOMAIN);

        $this->assertFalse($this->cookie->hasAttribute($name));
        $this->assertNull($this->cookie->getAttribute($name));
    }

    public function testSetCreatedAt()
    {
        $this->cookie->setCreatedAt($createdAt = time());

        $this->assertSame($createdAt, $this->cookie->getCreatedAt());
    }

    public function testGetAge()
    {
        $before = time() - $this->cookie->getCreatedAt();
        $age = $this->cookie->getAge();
        $after = time() - $this->cookie->getCreatedAt();

        $this->assertGreaterThanOrEqual($before, $age);
        $this->assertLessThanOrEqual($after, $age);
    }

    /**
     * @dataProvider expiredProvider
     */
    public function testIsExpired($createdAt, array $attributes, $expected)
    {
        $this->cookie->setCreatedAt($createdAt);
        $this->cookie->setAttributes($attributes);

        $this->assertSame($expected, $this->cookie->isExpired());
    }

    /**
     * @dataProvider matchDomainProvider
     */
    public function testMatchDomain(InternalRequestInterface $request, $domain, $expected)
    {
        if ($domain !== null) {
            $this->cookie->setAttributes(array(Cookie::ATTR_DOMAIN => $domain));
        } else {
            $this->cookie->clearAttributes();
        }

        $this->assertSame($expected, $this->cookie->matchDomain($request));
    }

    /**
     * @dataProvider matchPathProvider
     */
    public function testMatchPath(InternalRequestInterface $request, $path, $expected)
    {
        if ($path !== null) {
            $this->cookie->setAttributes(array(Cookie::ATTR_PATH => $path));
        } else {
            $this->cookie->clearAttributes();
        }

        $this->assertSame($expected, $this->cookie->matchPath($request));
    }

    /**
     * @dataProvider matchSecureProvider
     */
    public function testMatchSecure(InternalRequestInterface $request, $secure, $expected)
    {
        if ($secure !== null) {
            $this->cookie->setAttributes(array(Cookie::ATTR_SECURE => $secure));
        } else {
            $this->cookie->clearAttributes();
        }

        $this->assertSame($expected, $this->cookie->matchSecure($request));
    }

    /**
     * @dataProvider matchProvider
     */
    public function testMatch(InternalRequestInterface $request, array $attributes, $expected)
    {
        $this->cookie->setAttributes($attributes);

        $this->assertSame($expected, $this->cookie->match($request));
    }

    public function testToArray()
    {
        $this->assertSame(
            array(
                'name'       => $this->name,
                'value'      => $this->value,
                'attributes' => $this->attributes,
                'created_at' => $this->createdAt,
            ),
            $this->cookie->toArray()
        );
    }

    public function testToString()
    {
        $this->assertSame($this->name.'='.$this->value, (string) $this->cookie);
    }

    /**
     * Get the expired provider.
     *
     * @return array The expired provider.
     */
    public function expiredProvider()
    {
        $time = time();

        return array(
            array($time, array(), false),
            array($time, array(Cookie::ATTR_MAX_AGE => 1000), false),
            array($time, array(Cookie::ATTR_EXPIRES => date('D, d M Y H:i:s e', $time + 1000)), false),
            array(
                $time,
                array(
                    Cookie::ATTR_MAX_AGE => 1000,
                    Cookie::ATTR_EXPIRES => date('D, d M Y H:i:s e', $time + 1000),
                ),
                false,
            ),
            array($time, array(Cookie::ATTR_MAX_AGE => -1), true),
            array($time, array(Cookie::ATTR_EXPIRES => date('D, d M Y H:i:s e', $time - 1)), true),
            array(
                $time,
                array(
                    Cookie::ATTR_MAX_AGE => 1000,
                    Cookie::ATTR_EXPIRES => date('D, d M Y H:i:s e', $time - 1),
                ),
                true,
            ),
        );
    }

    /**
     * Gets the match domain provider.
     *
     * @return array The match domain provider.
     */
    public function matchDomainProvider()
    {
        return array(
            array(new InternalRequest('http://egeloen.fr'), null, true),
            array(new InternalRequest('http://egeloen.fr'), 'egeloen.fr', true),
            array(new InternalRequest('http://egeloen.fr'), '.egeloen.fr', true),
            array(new InternalRequest('http://egeloen.fr'), 'google.com', false),
            array(new InternalRequest('http://egeloen.fr'), '.google.com', false),
        );
    }

    /**
     * Gets the match path provider.
     *
     * @return array The match path provider.
     */
    public function matchPathProvider()
    {
        return array(
            array(new InternalRequest('http://egeloen.fr/path'), null, true),
            array(new InternalRequest('http://egeloen.fr/path'), '/path', true),
            array(new InternalRequest('http://egeloen.fr/path/foo'), '/path', true),
            array(new InternalRequest('http://egeloen.fr'), '/path', false),
        );
    }

    /**
     * Gets the match secure provider.
     *
     * @return array The match secure provider.
     */
    public function matchSecureProvider()
    {
        return array(
            array(new InternalRequest('http://egeloen.fr'), null, true),
            array(new InternalRequest('https://egeloen.fr'), null, true),
            array(new InternalRequest('http://egeloen.fr'), false, true),
            array(new InternalRequest('https://egeloen.fr'), true, true),
            array(new InternalRequest('http://egeloen.fr'), true, false),
            array(new InternalRequest('https://egeloen.fr'), false, false),
        );
    }

    /**
     * Gets the match provider.
     *
     * @return array The match provider.
     */
    public function matchProvider()
    {
        return array(
            array(new InternalRequest('http://egeloen.fr'), array(), true),
            array(new InternalRequest('http://egeloen.fr'), array(Cookie::ATTR_DOMAIN => 'egeloen.fr'), true),
            array(new InternalRequest('http://egeloen.fr'), array(Cookie::ATTR_DOMAIN => '.egeloen.fr'), true),
            array(new InternalRequest('http://egeloen.fr/path'), array(Cookie::ATTR_PATH => '/path'), true),
            array(new InternalRequest('http://egeloen.fr/path/foo'), array(Cookie::ATTR_PATH => '/path'), true),
            array(new InternalRequest('https://egeloen.fr'), array(Cookie::ATTR_SECURE => true), true),
            array(
                new InternalRequest('http://egeloen.fr/path/foo'),
                array(
                    Cookie::ATTR_DOMAIN => 'egeloen.fr',
                    Cookie::ATTR_PATH   => '/path',
                    Cookie::ATTR_SECURE => false,
                ),
                true,
            ),
            array(
                new InternalRequest('https://egeloen.fr/path/foo'),
                array(
                    Cookie::ATTR_DOMAIN => 'egeloen.fr',
                    Cookie::ATTR_PATH   => '/path',
                    Cookie::ATTR_SECURE => true,
                ),
                true,
            ),
            array(new InternalRequest('http://egeloen.fr'), array(Cookie::ATTR_DOMAIN => 'google.com'), false),
            array(new InternalRequest('http://egeloen.fr'), array(Cookie::ATTR_DOMAIN => '.google.com'), false),
            array(new InternalRequest('http://egeloen.fr'), array(Cookie::ATTR_PATH => '/path'), false),
            array(new InternalRequest('http://egeloen.fr'), array(Cookie::ATTR_SECURE => true), false),
            array(new InternalRequest('https://egeloen.fr'), array(Cookie::ATTR_SECURE => false), false),
            array(
                new InternalRequest('http://egeloen.fr/path/foo'),
                array(
                    Cookie::ATTR_DOMAIN => 'google.com',
                    Cookie::ATTR_PATH   => '/path',
                    Cookie::ATTR_SECURE => false,
                ),
                false,
            ),
            array(
                new InternalRequest('http://egeloen.fr/path/foo'),
                array(
                    Cookie::ATTR_DOMAIN => 'egeloen.fr',
                    Cookie::ATTR_PATH   => '/path/bar',
                    Cookie::ATTR_SECURE => false,
                ),
                false,
            ),
            array(
                new InternalRequest('http://egeloen.fr/path/foo'),
                array(
                    Cookie::ATTR_DOMAIN => 'egeloen.fr',
                    Cookie::ATTR_PATH   => '/path',
                    Cookie::ATTR_SECURE => true,
                ),
                false,
            ),
        );
    }
}
