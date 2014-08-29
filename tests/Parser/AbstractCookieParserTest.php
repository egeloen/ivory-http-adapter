<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Parser;

use Ivory\HttpAdapter\Event\Cookie\CookieInterface;

/**
 * Abstract cookie parser test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractCookieParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Gets the parse provider.
     *
     * @return array The parse provider.
     */
    public function parseProvider()
    {
        return array(
            array('', null, null),
            array(' ', null, null),
            array('=', null, null),
            array(' = ', null, null),
            array(';', null, null),
            array(' ; ', null, null),
            array('=;', null, null),
            array(' = ; ', null, null),
            array('foo=', 'foo', null),
            array(' foo = ', 'foo', null),
            array('foo=bar', 'foo', 'bar'),
            array(' foo = bar ', 'foo', 'bar'),
            array('foo=bar;domain=egeloen.fr', 'foo', 'bar', array(CookieInterface::ATTR_DOMAIN => 'egeloen.fr')),
            array(
                ' foo = bar ; domain = egeloen.fr ',
                'foo',
                'bar',
                array(CookieInterface::ATTR_DOMAIN => 'egeloen.fr'),
            ),
            array('foo=bar;path=/path', 'foo', 'bar', array(CookieInterface::ATTR_PATH => '/path')),
            array(' foo = bar ; path = /path ', 'foo', 'bar', array(CookieInterface::ATTR_PATH => '/path')),
            array('foo=bar;secure', 'foo', 'bar', array(CookieInterface::ATTR_SECURE => true)),
            array(' foo = bar ; secure ', 'foo', 'bar', array(CookieInterface::ATTR_SECURE => true)),
            array(
                'foo=bar;expires=Fri, 15 aug 2014 12:34:56 UTC',
                'foo',
                'bar',
                array(CookieInterface::ATTR_EXPIRES => 'Fri, 15 aug 2014 12:34:56 UTC'),
            ),
            array(
                ' foo = bar ; expires = Fri, 15 aug 2014 12:34:56 UTC ',
                'foo',
                'bar',
                array(CookieInterface::ATTR_EXPIRES => 'Fri, 15 aug 2014 12:34:56 UTC'),
            ),
            array('foo=bar;max-age=123', 'foo', 'bar', array(CookieInterface::ATTR_MAX_AGE => '123')),
            array(' foo = bar ; max-age = 123', 'foo', 'bar', array(CookieInterface::ATTR_MAX_AGE => '123')),
            array(
                'foo=bar;domain=egeloen.fr;path=/path;secure;expires=Fri, 15 aug 2014 12:34:56 UTC;max-age=123',
                'foo',
                'bar',
                array(
                    CookieInterface::ATTR_DOMAIN  => 'egeloen.fr',
                    CookieInterface::ATTR_PATH    => '/path',
                    CookieInterface::ATTR_SECURE  => true,
                    CookieInterface::ATTR_EXPIRES => 'Fri, 15 aug 2014 12:34:56 UTC',
                    CookieInterface::ATTR_MAX_AGE => '123',
                ),
            ),
            array(
                ' foo = bar ; domain = egeloen.fr ; path = /path ; secure ;'.
                ' expires = Fri, 15 aug 2014 12:34:56 UTC ; max-age = 123',
                'foo',
                'bar',
                array(
                    CookieInterface::ATTR_DOMAIN  => 'egeloen.fr',
                    CookieInterface::ATTR_PATH    => '/path',
                    CookieInterface::ATTR_SECURE  => true,
                    CookieInterface::ATTR_EXPIRES => 'Fri, 15 aug 2014 12:34:56 UTC',
                    CookieInterface::ATTR_MAX_AGE => '123',
                ),
            ),
        );
    }
}
