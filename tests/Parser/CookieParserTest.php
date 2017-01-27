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
 * @author GeLo <geloen.eric@gmail.com>
 */
class CookieParserTest extends AbstractCookieParserTest
{
    /**
     * @param string $header
     * @param string $name
     * @param mixed  $value
     * @param array  $attributes
     *
     * @dataProvider parseProvider
     */
    public function testParse($header, $name, $value, array $attributes = [])
    {
        $this->assertSame([$name, $value, $attributes], CookieParser::parse($header));
    }
}
