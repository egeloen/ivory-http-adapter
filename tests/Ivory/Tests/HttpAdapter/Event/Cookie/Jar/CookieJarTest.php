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
use Ivory\HttpAdapter\Event\Cookie\Jar\CookieJar;

/**
 * Cookie jar test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CookieJarTest extends AbstractCookieJarTest
{
    /** @var \Ivory\HttpAdapter\Event\Cookie\Jar\CookieJar */
    protected $cookieJar;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->cookieJar = new CookieJar();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->cookieJar);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Event\Cookie\CookieFactory', $this->cookieJar->getCookieFactory());

        $this->assertFalse($this->cookieJar->hasCookies());
        $this->assertEmpty($this->cookieJar->getCookies());

        $this->assertCount(0, $this->cookieJar);
        $this->assertEmpty(iterator_to_array($this->cookieJar));
    }

    public function testInitialState()
    {
        $this->cookieJar = new CookieJar($cookieFactory = $this->createCookieFactoryMock());

        $this->assertSame($cookieFactory, $this->cookieJar->getCookieFactory());
    }

    public function testSetCookieFactory()
    {
        $this->cookieJar->setCookieFactory($cookieFactory = $this->createCookieFactoryMock());

        $this->assertSame($cookieFactory, $this->cookieJar->getCookieFactory());
    }

    public function testSetCookies()
    {
        $this->cookieJar->setCookies(array($this->createCookieMock()));
        $this->cookieJar->setCookies($cookies = array($this->createCookieMock()));

        $this->assertTrue($this->cookieJar->hasCookies());
        $this->assertSame($cookies, $this->cookieJar->getCookies());

        $this->assertCount(1, $this->cookieJar);
        $this->assertSame($cookies, iterator_to_array($this->cookieJar));
    }

    public function testAddCookies()
    {
        $this->cookieJar->setCookies($cookies1 = array($this->createCookieMock()));
        $this->cookieJar->addCookies($cookies2 = array($this->createCookieMock()));

        $this->assertTrue($this->cookieJar->hasCookies());
        $this->assertSame(array_merge($cookies1, $cookies2), $this->cookieJar->getCookies());

        $this->assertCount(2, $this->cookieJar);
        $this->assertSame(array_merge($cookies1, $cookies2), iterator_to_array($this->cookieJar));
    }

    public function testRemoveCookies()
    {
        $this->cookieJar->setCookies($cookies = array($this->createCookieMock()));
        $this->cookieJar->removeCookies($cookies);

        $this->assertFalse($this->cookieJar->hasCookies());
        $this->assertEmpty($this->cookieJar->getCookies());

        $this->assertCount(0, $this->cookieJar);
        $this->assertEmpty(iterator_to_array($this->cookieJar));
    }

    public function testAddCookie()
    {
        $this->cookieJar->addCookie($cookie = $this->createCookieMock());

        $this->assertTrue($this->cookieJar->hasCookies());
        $this->assertTrue($this->cookieJar->hasCookie($cookie));
        $this->assertSame(array($cookie), $this->cookieJar->getCookies());

        $this->assertCount(1, $this->cookieJar);
        $this->assertSame(array($cookie), iterator_to_array($this->cookieJar));
    }

    public function testAddCookieWithoutName()
    {
        $this->cookieJar->addCookie($cookie = $this->createCookieMock(false));

        $this->assertFalse($this->cookieJar->hasCookies());
        $this->assertFalse($this->cookieJar->hasCookie($cookie));
        $this->assertEmpty($this->cookieJar->getCookies());

        $this->assertCount(0, $this->cookieJar);
        $this->assertEmpty(iterator_to_array($this->cookieJar));
    }

    public function testAddCookieWithoutValue()
    {
        $this->cookieJar->addCookie($this->createCookieMock());
        $this->cookieJar->addCookie($cookie = $this->createCookieMock(true, false));

        $this->assertFalse($this->cookieJar->hasCookies());
        $this->assertEmpty($this->cookieJar->getCookies());

        $this->assertCount(0, $this->cookieJar);
        $this->assertEmpty(iterator_to_array($this->cookieJar));
    }

    public function testAddCookieNotComparable()
    {
        $cookie1 = $this->createCookieMock();
        $cookie2 = $this->createCookieMock();

        $cookie2
            ->expects($this->any())
            ->method('compare')
            ->with($this->identicalTo($cookie1))
            ->will($this->returnValue(false));

        $this->cookieJar->addCookie($cookie1);
        $this->cookieJar->addCookie($cookie2);

        $this->assertTrue($this->cookieJar->hasCookies());
        $this->assertTrue($this->cookieJar->hasCookie($cookie1));
        $this->assertTrue($this->cookieJar->hasCookie($cookie2));
        $this->assertSame(array($cookie1, $cookie2), $this->cookieJar->getCookies());

        $this->assertCount(2, $this->cookieJar);
        $this->assertSame(array($cookie1, $cookie2), iterator_to_array($this->cookieJar));
    }

    public function testAddCookieComparable()
    {
        $cookie1 = $this->createCookieMock();
        $cookie2 = $this->createCookieMock();

        $cookie2
            ->expects($this->any())
            ->method('compare')
            ->with($this->identicalTo($cookie1))
            ->will($this->returnValue(true));

        $this->cookieJar->addCookie($cookie1);
        $this->cookieJar->addCookie($cookie2);

        $this->assertTrue($this->cookieJar->hasCookies());
        $this->assertTrue($this->cookieJar->hasCookie($cookie1));
        $this->assertFalse($this->cookieJar->hasCookie($cookie2));
        $this->assertSame(array($cookie1), $this->cookieJar->getCookies());

        $this->assertCount(1, $this->cookieJar);
        $this->assertSame(array($cookie1), iterator_to_array($this->cookieJar));
    }

    public function testAddCookieComparableLessExpired()
    {
        $cookie1 = $this->createCookieMock();
        $cookie1
            ->expects($this->any())
            ->method('getExpires')
            ->will($this->returnValue($expires = 0));

        $cookie2 = $this->createCookieMock();
        $cookie2
            ->expects($this->any())
            ->method('compare')
            ->with($this->identicalTo($cookie1))
            ->will($this->returnValue(true));

        $cookie2
            ->expects($this->any())
            ->method('getExpires')
            ->will($this->returnValue($expires + 1));

        $this->cookieJar->addCookie($cookie1);
        $this->cookieJar->addCookie($cookie2);

        $this->assertTrue($this->cookieJar->hasCookies());
        $this->assertFalse($this->cookieJar->hasCookie($cookie1));
        $this->assertTrue($this->cookieJar->hasCookie($cookie2));
        $this->assertSame(array($cookie2), $this->cookieJar->getCookies());

        $this->assertCount(1, $this->cookieJar);
        $this->assertSame(array($cookie2), iterator_to_array($this->cookieJar));
    }

    public function testAddCookieComparableWithDifferentValue()
    {
        $cookie1 = $this->createCookieMock();
        $cookie1
            ->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue('foo'));

        $cookie2 = $this->createCookieMock();
        $cookie2
            ->expects($this->any())
            ->method('compare')
            ->with($this->identicalTo($cookie1))
            ->will($this->returnValue(true));

        $cookie2
            ->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue('bar'));

        $this->cookieJar->addCookie($cookie1);
        $this->cookieJar->addCookie($cookie2);

        $this->assertTrue($this->cookieJar->hasCookies());
        $this->assertFalse($this->cookieJar->hasCookie($cookie1));
        $this->assertTrue($this->cookieJar->hasCookie($cookie2));
        $this->assertSame(array($cookie2), $this->cookieJar->getCookies());

        $this->assertCount(1, $this->cookieJar);
        $this->assertSame(array($cookie2), iterator_to_array($this->cookieJar));
    }

    public function testRemoveCookie()
    {
        $this->cookieJar->addCookie($cookie = $this->createCookieMock());
        $this->cookieJar->removeCookie($cookie);

        $this->assertFalse($this->cookieJar->hasCookies());
        $this->assertFalse($this->cookieJar->hasCookie($cookie));
        $this->assertEmpty($this->cookieJar->getCookies());

        $this->assertCount(0, $this->cookieJar);
        $this->assertEmpty(iterator_to_array($this->cookieJar));
    }

    public function testClearCookies()
    {
        $this->cookieJar->addCookie($this->createCookieMock());
        $this->cookieJar->clear();

        $this->assertFalse($this->cookieJar->hasCookies());
        $this->assertEmpty($this->cookieJar->getCookies());

        $this->assertCount(0, $this->cookieJar);
        $this->assertEmpty(iterator_to_array($this->cookieJar));
    }

    public function testClearCookiesByDomain()
    {
        $matchCookie = $this->createCookieMock();
        $matchCookie
            ->expects($this->once())
            ->method('matchDomain')
            ->with($this->identicalTo($domain = 'egeloen.fr'))
            ->will($this->returnValue(true));

        $unmatchCookie = $this->createCookieMock();
        $unmatchCookie
            ->expects($this->once())
            ->method('matchDomain')
            ->with($this->identicalTo($domain))
            ->will($this->returnValue(false));

        $this->cookieJar->addCookie($matchCookie);
        $this->cookieJar->addCookie($unmatchCookie);
        $this->cookieJar->clear($domain);

        $this->assertTrue($this->cookieJar->hasCookies());
        $this->assertSame(array($unmatchCookie), $this->cookieJar->getCookies());

        $this->assertCount(1, $this->cookieJar);
        $this->assertSame(array($unmatchCookie), iterator_to_array($this->cookieJar));
    }

    public function testClearCookiesByPath()
    {
        $matchCookie = $this->createCookieMock();
        $matchCookie
            ->expects($this->once())
            ->method('matchPath')
            ->with($this->identicalTo($path = '/path'))
            ->will($this->returnValue(true));

        $unmatchCookie = $this->createCookieMock();
        $unmatchCookie
            ->expects($this->once())
            ->method('matchPath')
            ->with($this->identicalTo($path))
            ->will($this->returnValue(false));

        $this->cookieJar->addCookie($matchCookie);
        $this->cookieJar->addCookie($unmatchCookie);
        $this->cookieJar->clear(null, $path);

        $this->assertTrue($this->cookieJar->hasCookies());
        $this->assertSame(array($unmatchCookie), $this->cookieJar->getCookies());

        $this->assertCount(1, $this->cookieJar);
        $this->assertSame(array($unmatchCookie), iterator_to_array($this->cookieJar));
    }

    public function testClearCookiesByName()
    {
        $matchCookie = $this->createCookieMock();
        $matchCookie
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'foo'));

        $unmatchCookie = $this->createCookieMock();
        $unmatchCookie
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $this->cookieJar->addCookie($matchCookie);
        $this->cookieJar->addCookie($unmatchCookie);
        $this->cookieJar->clear(null, null, $name);

        $this->assertTrue($this->cookieJar->hasCookies());
        $this->assertSame(array($unmatchCookie), $this->cookieJar->getCookies());

        $this->assertCount(1, $this->cookieJar);
        $this->assertSame(array($unmatchCookie), iterator_to_array($this->cookieJar));
    }

    public function testClearCookiesByDomainAndPathAndName()
    {
        $cookie = $this->createCookieMock();
        $cookie
            ->expects($this->once())
            ->method('matchDomain')
            ->with($this->identicalTo($domain = 'egeloen.fr'))
            ->will($this->returnValue(true));

        $cookie
            ->expects($this->once())
            ->method('matchPath')
            ->with($this->identicalTo($path = '/path'))
            ->will($this->returnValue(true));

        $cookie
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'foo'));

        $this->cookieJar->addCookie($cookie);
        $this->cookieJar->clear($domain, $path, $name);

        $this->assertFalse($this->cookieJar->hasCookies());
        $this->assertEmpty($this->cookieJar->getCookies());

        $this->assertCount(0, $this->cookieJar);
        $this->assertEmpty(iterator_to_array($this->cookieJar));
    }

    public function testClean()
    {
        $this->cookieJar->addCookie($cookie = $this->createCookieMock());
        $this->cookieJar->addCookie($expired = $this->createExpiredCookieMock());
        $this->cookieJar->clean();

        $this->assertTrue($this->cookieJar->hasCookies());
        $this->assertSame(array($cookie), $this->cookieJar->getCookies());

        $this->assertCount(1, $this->cookieJar);
        $this->assertSame(array($cookie), iterator_to_array($this->cookieJar));
    }

    public function testPopulate()
    {
        $request = $this->createRequestMock();

        $this->cookieJar->addCookie($expiredCookie = $this->createExpiredCookieMock());
        $this->cookieJar->addCookie($unmatchCookie = $this->createCookieMock());
        $this->cookieJar->addCookie($matchCookie = $this->createCookieMock());

        $expiredCookie
            ->expects($this->never())
            ->method('match');

        $unmatchCookie
            ->expects($this->once())
            ->method('match')
            ->with($this->identicalTo($request))
            ->will($this->returnValue(false));

        $matchCookie
            ->expects($this->once())
            ->method('match')
            ->with($this->identicalTo($request))
            ->will($this->returnValue(true));

        $matchCookie
            ->expects($this->once())
            ->method('__toString')
            ->will($this->returnValue('foo=bar'));

        $request
            ->expects($this->once())
            ->method('addHeader')
            ->with(
                $this->identicalTo('Cookie'),
                $this->identicalTo('foo=bar')
            );

        $this->cookieJar->populate($request);
    }

    public function testExtract()
    {
        $response = $this->createResponseMock();
        $response
            ->expects($this->once())
            ->method('getHeaderAsArray')
            ->with($this->identicalTo('Set-Cookie'))
            ->will($this->returnValue(array('foo', 'bar')));

        $this->cookieJar->setCookieFactory($cookieFactory = $this->createCookieFactoryMock());

        $cookieFactory
            ->expects($this->exactly(2))
            ->method('parse')
            ->will($this->returnValueMap(array(
                array('foo', $cookie1 = $this->createCookieMock()),
                array('bar', $cookie2 = $this->createCookieMock()),
            )));

        $cookie1
            ->expects($this->once())
            ->method('hasAttribute')
            ->with($this->identicalTo(CookieInterface::ATTR_DOMAIN))
            ->will($this->returnValue(true));

        $cookie2
            ->expects($this->once())
            ->method('hasAttribute')
            ->with($this->identicalTo(CookieInterface::ATTR_DOMAIN))
            ->will($this->returnValue(false));

        $cookie2
            ->expects($this->once())
            ->method('setAttribute')
            ->with($this->identicalTo(CookieInterface::ATTR_DOMAIN), $this->identicalTo('egeloen.fr'));

        $request = $this->createRequestMock();
        $request
            ->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue('http://egeloen.fr/path'));

        $this->cookieJar->extract($request, $response);

        $this->assertSame(array($cookie1, $cookie2), $this->cookieJar->getCookies());
    }

    /**
     * Creates a request mock.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject The request mock.
     */
    protected function createRequestMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
    }

    /**
     * Creates a response mock.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject The response mock.
     */
    protected function createResponseMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }
}
