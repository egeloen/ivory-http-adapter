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

use Ivory\HttpAdapter\Event\Cookie\CookieFactoryInterface;
use Ivory\HttpAdapter\Event\Cookie\CookieInterface;
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractCookieJarTest extends AbstractTestCase
{
    /**
     * @return CookieFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createCookieFactoryMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Event\Cookie\CookieFactoryInterface');
    }

    /**
     * @param bool $name
     * @param bool $value
     *
     * @return CookieInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createCookieMock($name = true, $value = true)
    {
        $cookie = $this->createMock('Ivory\HttpAdapter\Event\Cookie\CookieInterface');

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
     * @return CookieInterface|\PHPUnit_Framework_MockObject_MockObject
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
