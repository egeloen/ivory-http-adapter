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

use Ivory\HttpAdapter\Parser\StatusLineParser;

/**
 * Status line parser test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StatusLineParserTest extends AbstractParserTest
{
    /**
     * @dataProvider headersProvider
     */
    public function testParse($headers)
    {
        $this->assertSame('HTTP/1.1 200 OK', StatusLineParser::parse($headers));
    }
}
