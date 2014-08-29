<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Extractor;

use Ivory\HttpAdapter\Extractor\ProtocolVersionExtractor;
use Ivory\Tests\HttpAdapter\Parser\AbstractHeadersParserTest;

/**
 * Protocol version extractor test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ProtocolVersionExtractorTest extends AbstractHeadersParserTest
{
    /**
     * @dataProvider headersProvider
     */
    public function testExtract($headers)
    {
        $this->assertSame('1.1', ProtocolVersionExtractor::extract($headers));
    }
}
