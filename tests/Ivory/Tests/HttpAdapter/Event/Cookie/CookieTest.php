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

    public function testInitialState()
    {
        $this->assertTrue($this->cookie->hasName());
        $this->assertSame($this->name, $this->cookie->getName());

        $this->assertTrue($this->cookie->hasValue());
        $this->assertSame($this->value, $this->cookie->getValue());

        $this->assertTrue($this->cookie->hasAttributes());
        $this->assertSame($this->attributes, $this->cookie->getAttributes());

        foreach ($this->attributes as $name => $value) {
            $this->assertTrue($this->cookie->hasAttribute($name));
            $this->assertSame($value, $this->cookie->getAttribute($name));
        }

        $this->assertSame($this->createdAt, $this->cookie->getCreatedAt());
    }

    public function testSetName()
    {
        $this->cookie->setName($name = 'foo');

        $this->assertTrue($this->cookie->hasName());
        $this->assertSame($name, $this->cookie->getName());
    }

    public function testSetNameEmpty()
    {
        $this->cookie->setName(null);

        $this->assertFalse($this->cookie->hasName());
        $this->assertNull($this->cookie->getName());
    }

    public function testSetValue()
    {
        $this->cookie->setValue($value = 'foo');

        $this->assertTrue($this->cookie->hasValue());
        $this->assertSame($value, $this->cookie->getValue());
    }

    public function testSetValueEmpty()
    {
        $this->cookie->setValue(null);

        $this->assertFalse($this->cookie->hasValue());
        $this->assertNull($this->cookie->getValue());
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

    /**
     * @dataProvider expiresProvider
     */
    public function testGetExpires($createdAt, array $attributes, $expected)
    {
        $this->cookie->setCreatedAt($createdAt);
        $this->cookie->setAttributes($attributes);

        $this->assertSame($expected, $this->cookie->getExpires());
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
     * @dataProvider compareProvider
     */
    public function testCompare($name, $domain, $path, $cookieName, $cookieDomain, $cookiePath, $expected)
    {
        $this->cookie->setName($name);
        $this->cookie->setAttributes(array(
            Cookie::ATTR_DOMAIN => $domain,
            Cookie::ATTR_PATH   => $path,
        ));

        $cookie = new Cookie(
            $cookieName,
            'value',
            array(
                Cookie::ATTR_DOMAIN => $cookieDomain,
                Cookie::ATTR_PATH   => $cookiePath,
            ),
            time()
        );

        $this->assertSame($expected, $this->cookie->compare($cookie));
    }

    /**
     * @dataProvider matchDomainProvider
     */
    public function testMatchDomain($domain, $cookieDomain, $expected)
    {
        $this->cookie->setAttributes(array(Cookie::ATTR_DOMAIN => $cookieDomain));

        $this->assertSame($expected, $this->cookie->matchDomain($domain));
    }

    /**
     * @dataProvider matchPathProvider
     */
    public function testMatchPath($path, $cookiePath, $expected)
    {
        $this->cookie->setAttributes(array(Cookie::ATTR_PATH => $cookiePath));

        $this->assertSame($expected, $this->cookie->matchPath($path));
    }

    /**
     * @dataProvider matchSchemeProvider
     */
    public function testMatchScheme($scheme, $secure, $expected)
    {
        $this->cookie->setAttributes(array(Cookie::ATTR_SECURE => $secure));

        $this->assertSame($expected, $this->cookie->matchScheme($scheme));
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
     * Gets the expires provider.
     *
     * @return array The expires provider.
     */
    public function expiresProvider()
    {
        $createdAt = time();
        $age = 10;
        $expires = $createdAt + $age;

        return array(
            array($createdAt, array(Cookie::ATTR_MAX_AGE => $age), $expires),
            array($createdAt, array(Cookie::ATTR_EXPIRES => date('D, d M Y H:i:s e', $expires)), $expires),
            array($createdAt, array(), false),
        );
    }

    /**
     * Get the expired provider.
     *
     * @return array The expired provider.
     */
    public function expiredProvider()
    {
        $createdAt = time();
        $ageExpired = 1000;
        $ageNotExpired = -1;
        $expiresExpired = $createdAt + $ageExpired;
        $expiresNotExpired = $createdAt + $ageNotExpired;

        return array(
            array($createdAt, array(), false),
            array($createdAt, array(Cookie::ATTR_MAX_AGE => $ageExpired), false),
            array($createdAt, array(Cookie::ATTR_EXPIRES => date('D, d M Y H:i:s e', $expiresExpired)), false),
            array(
                $createdAt,
                array(
                    Cookie::ATTR_MAX_AGE => $ageExpired,
                    Cookie::ATTR_EXPIRES => date('D, d M Y H:i:s e', $expiresExpired),
                ),
                false,
            ),
            array($createdAt, array(Cookie::ATTR_MAX_AGE => $ageNotExpired), true),
            array($createdAt, array(Cookie::ATTR_EXPIRES => date('D, d M Y H:i:s e', $expiresNotExpired)), true),
            array(
                $createdAt,
                array(
                    Cookie::ATTR_MAX_AGE => $ageExpired,
                    Cookie::ATTR_EXPIRES => date('D, d M Y H:i:s e', $expiresNotExpired),
                ),
                true,
            ),
        );
    }

    /**
     * Gets the compare provider.
     *
     * @return array The compare provider.
     */
    public function compareProvider()
    {
        return array(
            array(null, null, null, null, null, null, true),
            array('foo', null, null, 'foo', null, null, true),
            array(null, 'foo', null, null, 'foo', null, true),
            array(null, null, 'foo', null, null, 'foo', true),
            array('foo', 'bar', null, 'foo', 'bar', null, true),
            array('foo', 'bar', 'baz', 'foo', 'bar', 'baz', true),
            array('foo', null, null, 'bar', null, null, false),
            array(null, 'foo', null, null, 'bar', null, false),
            array(null, null, 'foo', null, null, 'bar', false),
            array('foo', 'bar', null, 'foo', 'baz', null, false),
            array('foo', 'bar', 'baz', 'foo', 'bar', 'bat', false),
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
            array(null, null, true),
            array('egeloen.fr', null, true),
            array('egeloen.fr', 'egeloen.fr', true),
            array('egeloen.fr', '.egeloen.fr', true),
            array('egeloen.fr', 'google.com', false),
            array('egeloen.fr', '.google.com', false),
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
            array(null, null, true),
            array('/path', null, true),
            array('/path', '/path', true),
            array('/path/foo', '/path', true),
            array(null, '/path', false),
            array('/foo', '/path', false),
            array('/foo', '/path/foo', false),
        );
    }

    /**
     * Gets the match scheme provider.
     *
     * @return array The match scheme provider.
     */
    public function matchSchemeProvider()
    {
        return array(
            array(null, null, true),
            array(null, false, true),
            array(null, true, false),
            array('http', null, true),
            array('https', null, true),
            array('http', false, true),
            array('https', true, true),
            array('http', true, false),
            array('https', false, false),
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
