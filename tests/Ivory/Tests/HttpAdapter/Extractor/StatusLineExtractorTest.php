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

use Ivory\HttpAdapter\Extractor\StatusLineExtractor;
use Ivory\Tests\HttpAdapter\Parser\AbstractHeadersParserTest;

/**
 * Status line extractor test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StatusLineExtractorTest extends AbstractHeadersParserTest
{
    /**
     * @dataProvider headersProvider
     */
    public function testExtract($headers)
    {
        $this->assertSame('HTTP/1.1 200 OK', StatusLineExtractor::extract($headers));
    }
}
