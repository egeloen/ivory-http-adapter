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
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractCookieParserTest extends AbstractTestCase
{
    /**
     * @return array
     */
    public function parseProvider()
    {
        return [
            ['', null, null],
            [' ', null, null],
            ['=', null, null],
            [' = ', null, null],
            [';', null, null],
            [' ; ', null, null],
            ['=;', null, null],
            [' = ; ', null, null],
            ['foo=', 'foo', null],
            [' foo = ', 'foo', null],
            ['foo=bar', 'foo', 'bar'],
            [' foo = bar ', 'foo', 'bar'],
            ['foo=bar;domain=egeloen.fr', 'foo', 'bar', [CookieInterface::ATTR_DOMAIN => 'egeloen.fr']],
            [
                ' foo = bar ; domain = egeloen.fr ',
                'foo',
                'bar',
                [CookieInterface::ATTR_DOMAIN => 'egeloen.fr'],
            ],
            ['foo=bar;path=/path', 'foo', 'bar', [CookieInterface::ATTR_PATH => '/path']],
            [' foo = bar ; path = /path ', 'foo', 'bar', [CookieInterface::ATTR_PATH => '/path']],
            ['foo=bar;secure', 'foo', 'bar', [CookieInterface::ATTR_SECURE => true]],
            [' foo = bar ; secure ', 'foo', 'bar', [CookieInterface::ATTR_SECURE => true]],
            [
                'foo=bar;expires=Fri, 15 aug 2014 12:34:56 UTC',
                'foo',
                'bar',
                [CookieInterface::ATTR_EXPIRES => 'Fri, 15 aug 2014 12:34:56 UTC'],
            ],
            [
                ' foo = bar ; expires = Fri, 15 aug 2014 12:34:56 UTC ',
                'foo',
                'bar',
                [CookieInterface::ATTR_EXPIRES => 'Fri, 15 aug 2014 12:34:56 UTC'],
            ],
            ['foo=bar;max-age=123', 'foo', 'bar', [CookieInterface::ATTR_MAX_AGE => '123']],
            [' foo = bar ; max-age = 123', 'foo', 'bar', [CookieInterface::ATTR_MAX_AGE => '123']],
            [
                'foo=bar;domain=egeloen.fr;path=/path;secure;expires=Fri, 15 aug 2014 12:34:56 UTC;max-age=123',
                'foo',
                'bar',
                [
                    CookieInterface::ATTR_DOMAIN  => 'egeloen.fr',
                    CookieInterface::ATTR_PATH    => '/path',
                    CookieInterface::ATTR_SECURE  => true,
                    CookieInterface::ATTR_EXPIRES => 'Fri, 15 aug 2014 12:34:56 UTC',
                    CookieInterface::ATTR_MAX_AGE => '123',
                ],
            ],
            [
                ' foo = bar ; domain = egeloen.fr ; path = /path ; secure ;'.
                ' expires = Fri, 15 aug 2014 12:34:56 UTC ; max-age = 123',
                'foo',
                'bar',
                [
                    CookieInterface::ATTR_DOMAIN  => 'egeloen.fr',
                    CookieInterface::ATTR_PATH    => '/path',
                    CookieInterface::ATTR_SECURE  => true,
                    CookieInterface::ATTR_EXPIRES => 'Fri, 15 aug 2014 12:34:56 UTC',
                    CookieInterface::ATTR_MAX_AGE => '123',
                ],
            ],
        ];
    }
}
