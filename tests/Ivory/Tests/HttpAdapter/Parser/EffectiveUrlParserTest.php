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

use Ivory\HttpAdapter\Parser\EffectiveUrlParser;

/**
 * Effective url parser test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class EffectiveUrlParserTest extends AbstractParserTest
{
    /**
     * @dataProvider simpleHeadersProvider
     */
    public function testParseWithoutLocation($headers)
    {
        $this->assertSame($this->getUrl(), EffectiveUrlParser::parse($headers, $this->getUrl(), true));
    }

    /**
     * @dataProvider redirectHeadersProvider
     */
    public function testParseWithLocation($headers)
    {
        $this->assertSame($this->getRedirectLocation(), EffectiveUrlParser::parse($headers, $this->getUrl(), true));
    }

    /**
     * @dataProvider redirectHeadersProvider
     */
    public function testParseWithLocationButWithMaxRedirects($headers)
    {
        $this->assertSame($this->getUrl(), EffectiveUrlParser::parse($headers, $this->getUrl(), false));
    }

    /**
     * Gets the url.
     *
     * @return string The url.
     */
    protected function getUrl()
    {
        return 'http://www.google.fr/';
    }
}
