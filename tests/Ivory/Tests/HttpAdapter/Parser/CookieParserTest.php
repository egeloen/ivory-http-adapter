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

use Ivory\HttpAdapter\Parser\CookieParser;

/**
 * Cookie parser test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CookieParserTest extends AbstractCookieParserTest
{
    /**
     * @dataProvider parseProvider
     */
    public function testParse($header, $name, $value, array $attributes = array())
    {
        $this->assertSame(array($name, $value, $attributes), CookieParser::parse($header));
    }
}
