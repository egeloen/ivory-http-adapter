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
class UrlNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalizeWithHttpScheme()
    {
        $url = 'http://egeloen.fr/';

        $this->assertSame($url, UrlNormalizer::normalize($url));
    }

    public function testNormalizeWithHttpsScheme()
    {
        $url = 'https://egeloen.fr/';

        $this->assertSame($url, UrlNormalizer::normalize($url));
    }

    public function testNormalizeWithoutScheme()
    {
        $url = 'egeloen.fr';

        $this->assertSame('http://'.$url, UrlNormalizer::normalize($url));
    }
}
