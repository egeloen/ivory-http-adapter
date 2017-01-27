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
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CookieTest extends AbstractTestCase
{
    /**
     * @var Cookie
     */
    private $cookie;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @var array
     */
    private $attributes;

    /**
     * @var int
     */
    private $createdAt;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->cookie = new Cookie(
            $this->name = 'name',
            $this->value = 'value',
            $this->attributes = [
                Cookie::ATTR_DOMAIN  => 'egeloen.fr',
                Cookie::ATTR_PATH    => '/',
                Cookie::ATTR_SECURE  => false,
                Cookie::ATTR_EXPIRES => date('D, d M Y H:i:s e', time() + 100),
                Cookie::ATTR_MAX_AGE => 100,
            ],
            $this->createdAt = time()
        );
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
        $this->cookie->setAttributes($attributes = [Cookie::ATTR_DOMAIN => 'foo']);

        $this->assertSame($attributes, $this->cookie->getAttributes());
    }

    public function testAddAttributes()
    {
        $this->cookie->addAttributes($attributes = [Cookie::ATTR_DOMAIN => 'foo']);

        $this->assertSame(array_merge($this->attributes, $attributes), $this->cookie->getAttributes());
    }

    public function testRemoveAttributes()
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
     * @param int   $createdAt
     * @param array $attributes
     * @param mixed $expected
     *
     * @dataProvider expiresProvider
     */
    public function testGetExpires($createdAt, array $attributes, $expected)
    {
        $this->cookie->setCreatedAt($createdAt);
        $this->cookie->setAttributes($attributes);

        $this->assertSame($expected, $this->cookie->getExpires());
    }

    /**
     * @param int   $createdAt
     * @param array $attributes
     * @param mixed $expected
     *
     * @dataProvider expiredProvider
     */
    public function testIsExpired($createdAt, array $attributes, $expected)
    {
        $this->cookie->setCreatedAt($createdAt);
        $this->cookie->setAttributes($attributes);

        $this->assertSame($expected, $this->cookie->isExpired());
    }

    /**
     * @param string $name
     * @param string $domain
     * @param string $path
     * @param string $cookieName
     * @param string $cookieDomain
     * @param string $cookiePath
     * @param bool   $expected
     *
     * @dataProvider compareProvider
     */
    public function testCompare($name, $domain, $path, $cookieName, $cookieDomain, $cookiePath, $expected)
    {
        $this->cookie->setName($name);
        $this->cookie->setAttributes([
            Cookie::ATTR_DOMAIN => $domain,
            Cookie::ATTR_PATH   => $path,
        ]);

        $cookie = new Cookie(
            $cookieName,
            'value',
            [
                Cookie::ATTR_DOMAIN => $cookieDomain,
                Cookie::ATTR_PATH   => $cookiePath,
            ],
            time()
        );

        $this->assertSame($expected, $this->cookie->compare($cookie));
    }

    /**
     * @param string $domain
     * @param string $cookieDomain
     * @param bool   $expected
     *
     * @dataProvider matchDomainProvider
     */
    public function testMatchDomain($domain, $cookieDomain, $expected)
    {
        $this->cookie->setAttributes([Cookie::ATTR_DOMAIN => $cookieDomain]);

        $this->assertSame($expected, $this->cookie->matchDomain($domain));
    }

    /**
     * @param string $path
     * @param string $cookiePath
     * @param bool   $expected
     *
     * @dataProvider matchPathProvider
     */
    public function testMatchPath($path, $cookiePath, $expected)
    {
        $this->cookie->setAttributes([Cookie::ATTR_PATH => $cookiePath]);

        $this->assertSame($expected, $this->cookie->matchPath($path));
    }

    /**
     * @param string $scheme
     * @param bool   $secure
     * @param bool   $expected
     *
     * @dataProvider matchSchemeProvider
     */
    public function testMatchScheme($scheme, $secure, $expected)
    {
        $this->cookie->setAttributes([Cookie::ATTR_SECURE => $secure]);

        $this->assertSame($expected, $this->cookie->matchScheme($scheme));
    }

    /**
     * @param InternalRequestInterface $request
     * @param array                    $attributes
     * @param bool                     $expected
     *
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
            [
                'name'       => $this->name,
                'value'      => $this->value,
                'attributes' => $this->attributes,
                'created_at' => $this->createdAt,
            ],
            $this->cookie->toArray()
        );
    }

    public function testToString()
    {
        $this->assertSame($this->name.'='.$this->value, (string) $this->cookie);
    }

    /**
     * @return array
     */
    public function expiresProvider()
    {
        $createdAt = time();
        $age = 10;
        $expires = $createdAt + $age;

        return [
            [$createdAt, [Cookie::ATTR_MAX_AGE => $age], $expires],
            [$createdAt, [Cookie::ATTR_EXPIRES => date('D, d M Y H:i:s e', $expires)], $expires],
            [$createdAt, [], false],
        ];
    }

    /**
     * @return array
     */
    public function expiredProvider()
    {
        $createdAt = time();
        $ageExpired = 1000;
        $ageNotExpired = -1;
        $expiresExpired = $createdAt + $ageExpired;
        $expiresNotExpired = $createdAt + $ageNotExpired;

        return [
            [$createdAt, [], false],
            [$createdAt, [Cookie::ATTR_MAX_AGE => $ageExpired], false],
            [$createdAt, [Cookie::ATTR_EXPIRES => date('D, d M Y H:i:s e', $expiresExpired)], false],
            [
                $createdAt,
                [
                    Cookie::ATTR_MAX_AGE => $ageExpired,
                    Cookie::ATTR_EXPIRES => date('D, d M Y H:i:s e', $expiresExpired),
                ],
                false,
            ],
            [$createdAt, [Cookie::ATTR_MAX_AGE => $ageNotExpired], true],
            [$createdAt, [Cookie::ATTR_EXPIRES => date('D, d M Y H:i:s e', $expiresNotExpired)], true],
            [
                $createdAt,
                [
                    Cookie::ATTR_MAX_AGE => $ageExpired,
                    Cookie::ATTR_EXPIRES => date('D, d M Y H:i:s e', $expiresNotExpired),
                ],
                true,
            ],
        ];
    }

    /**
     * @return array
     */
    public function compareProvider()
    {
        return [
            [null, null, null, null, null, null, true],
            ['foo', null, null, 'foo', null, null, true],
            [null, 'foo', null, null, 'foo', null, true],
            [null, null, 'foo', null, null, 'foo', true],
            ['foo', 'bar', null, 'foo', 'bar', null, true],
            ['foo', 'bar', 'baz', 'foo', 'bar', 'baz', true],
            ['foo', null, null, 'bar', null, null, false],
            [null, 'foo', null, null, 'bar', null, false],
            [null, null, 'foo', null, null, 'bar', false],
            ['foo', 'bar', null, 'foo', 'baz', null, false],
            ['foo', 'bar', 'baz', 'foo', 'bar', 'bat', false],
        ];
    }

    /**
     * @return array
     */
    public function matchDomainProvider()
    {
        return [
            [null, null, true],
            ['egeloen.fr', null, true],
            ['egeloen.fr', 'egeloen.fr', true],
            ['egeloen.fr', '.egeloen.fr', true],
            ['egeloen.fr', 'google.com', false],
            ['egeloen.fr', '.google.com', false],
        ];
    }

    /**
     * @return array
     */
    public function matchPathProvider()
    {
        return [
            [null, null, true],
            ['/path', null, true],
            ['/path', '/path', true],
            ['/path/foo', '/path', true],
            [null, '/path', false],
            ['/foo', '/path', false],
            ['/foo', '/path/foo', false],
        ];
    }

    /**
     * @return array
     */
    public function matchSchemeProvider()
    {
        return [
            [null, null, true],
            [null, false, true],
            [null, true, false],
            ['http', null, true],
            ['https', null, true],
            ['http', false, true],
            ['https', true, true],
            ['http', true, false],
            ['https', false, false],
        ];
    }

    /**
     * @return array
     */
    public function matchProvider()
    {
        return [
            [new InternalRequest('http://egeloen.fr'), [], true],
            [new InternalRequest('http://egeloen.fr'), [Cookie::ATTR_DOMAIN => 'egeloen.fr'], true],
            [new InternalRequest('http://egeloen.fr'), [Cookie::ATTR_DOMAIN => '.egeloen.fr'], true],
            [new InternalRequest('http://egeloen.fr/path'), [Cookie::ATTR_PATH => '/path'], true],
            [new InternalRequest('http://egeloen.fr/path/foo'), [Cookie::ATTR_PATH => '/path'], true],
            [new InternalRequest('https://egeloen.fr'), [Cookie::ATTR_SECURE => true], true],
            [
                new InternalRequest('http://egeloen.fr/path/foo'),
                [
                    Cookie::ATTR_DOMAIN => 'egeloen.fr',
                    Cookie::ATTR_PATH   => '/path',
                    Cookie::ATTR_SECURE => false,
                ],
                true,
            ],
            [
                new InternalRequest('https://egeloen.fr/path/foo'),
                [
                    Cookie::ATTR_DOMAIN => 'egeloen.fr',
                    Cookie::ATTR_PATH   => '/path',
                    Cookie::ATTR_SECURE => true,
                ],
                true,
            ],
            [new InternalRequest('http://egeloen.fr'), [Cookie::ATTR_DOMAIN => 'google.com'], false],
            [new InternalRequest('http://egeloen.fr'), [Cookie::ATTR_DOMAIN => '.google.com'], false],
            [new InternalRequest('http://egeloen.fr'), [Cookie::ATTR_PATH => '/path'], false],
            [new InternalRequest('http://egeloen.fr'), [Cookie::ATTR_SECURE => true], false],
            [new InternalRequest('https://egeloen.fr'), [Cookie::ATTR_SECURE => false], false],
            [
                new InternalRequest('http://egeloen.fr/path/foo'),
                [
                    Cookie::ATTR_DOMAIN => 'google.com',
                    Cookie::ATTR_PATH   => '/path',
                    Cookie::ATTR_SECURE => false,
                ],
                false,
            ],
            [
                new InternalRequest('http://egeloen.fr/path/foo'),
                [
                    Cookie::ATTR_DOMAIN => 'egeloen.fr',
                    Cookie::ATTR_PATH   => '/path/bar',
                    Cookie::ATTR_SECURE => false,
                ],
                false,
            ],
            [
                new InternalRequest('http://egeloen.fr/path/foo'),
                [
                    Cookie::ATTR_DOMAIN => 'egeloen.fr',
                    Cookie::ATTR_PATH   => '/path',
                    Cookie::ATTR_SECURE => true,
                ],
                false,
            ],
        ];
    }
}
