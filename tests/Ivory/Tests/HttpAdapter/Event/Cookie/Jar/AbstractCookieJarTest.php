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
 * Abstract cookie jar test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractCookieJarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates a cookie factory mock.
     *
     * @return \Ivory\HttpAdapter\Event\Cookie\CookieFactoryInterface|\PHPUnit_Framework_MockObject_MockObject The cookie factory mock.
     */
    protected function createCookieFactoryMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Event\Cookie\CookieFactoryInterface');
    }

    /**
     * Creates a cookie mock.
     *
     * @param boolean $name  TRUE if the cookie has a name else FALSE.
     * @param boolean $value TRUE if the cookie has a value else FALSE.
     *
     * @return \Ivory\HttpAdapter\Event\Cookie\CookieInterface|\PHPUnit_Framework_MockObject_MockObject The cookie mock.
     */
    protected function createCookieMock($name = true, $value = true)
    {
        $cookie = $this->getMock('Ivory\HttpAdapter\Event\Cookie\CookieInterface');

        if ($name) {
            $cookie
                ->expects($this->any())
                ->method('hasName')
                ->will($this->returnValue(true));
        }

        if ($value) {
            $cookie
                ->expects($this->any())
                ->method('hasValue')
                ->will($this->returnValue(true));
        }

        return $cookie;
    }

    /**
     * Creates an expired cookie mock.
     *
     * @return \Ivory\HttpAdapter\Event\Cookie\CookieInterface|\PHPUnit_Framework_MockObject_MockObject The expired cookie mock.
     */
    protected function createExpiredCookieMock()
    {
        $cookie = $this->createCookieMock();
        $cookie
            ->expects($this->any())
            ->method('isExpired')
            ->will($this->returnValue(true));

        return $cookie;
    }
}
