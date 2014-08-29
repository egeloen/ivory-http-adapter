<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Normalizer;

use Ivory\HttpAdapter\Normalizer\UrlNormalizer;

/**
 * Url normalizer test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class UrlNormalizerTest extends AbstractUrlNormalizerTest
{
    /**
     * @dataProvider validUrlProvider
     */
    public function testNormalizeWithValidUrl($url)
    {
        $this->assertSame($url, UrlNormalizer::normalize($url));
    }

    /**
     * @dataProvider invalidUrlProvider
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testNormalizeWithInvalidUrl($url)
    {
        UrlNormalizer::normalize($url);
    }
}
