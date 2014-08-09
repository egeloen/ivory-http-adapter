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

use Ivory\HttpAdapter\Parser\StatusCodeParser;

/**
 * Status code parser test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StatusCodeParserTest extends AbstractParserTest
{
    /**
     * @dataProvider headersProvider
     */
    public function testParse($headers)
    {
        $this->assertSame(200, StatusCodeParser::parse($headers));
    }
}
