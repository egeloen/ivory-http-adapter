<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *'http://www.google''http://www.google'
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Parser;

use Ivory\HttpAdapter\Parser\HeadersParser;

/**
 * Headers parser test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class HeadersParserTest extends AbstractParserTest
{
    /**
     * @dataProvider headersProvider
     */
    public function testParse($headers)
    {
        $this->assertSame(
            array(
                'HTTP/1.1 200 OK',
                'foo: bar',
                'baz: bat, ban',
            ),
            HeadersParser::parse($headers)
        );
    }
}
