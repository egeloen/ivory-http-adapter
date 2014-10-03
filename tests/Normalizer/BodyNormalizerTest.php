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

use Ivory\HttpAdapter\Message\RequestInterface;
use Ivory\HttpAdapter\Normalizer\BodyNormalizer;

/**
 * Body normalizer test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class BodyNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalizeWithResource()
    {
        $body = fopen('php://temp', 'r');

        $this->assertSame($body, BodyNormalizer::normalize($body, RequestInterface::METHOD_GET));
    }

    public function testNormalizeWithString()
    {
        $body = 'foo';

        $this->assertSame($body, BodyNormalizer::normalize($body, RequestInterface::METHOD_GET));
    }

    public function testNormalizeWithStream()
    {
        $body = $this->getMock('Ivory\HttpAdapter\Message\Stream\StreamableInterface');

        $this->assertSame($body, BodyNormalizer::normalize($body, RequestInterface::METHOD_GET));
    }

    public function testNormalizeWithCallable()
    {
        $result = 'foo';
        $body = function () use ($result) {
            return $result;
        };

        $this->assertSame($result, BodyNormalizer::normalize($body, RequestInterface::METHOD_GET));
    }

    public function testNormalizeWithHeadMethod()
    {
        $this->assertNull(BodyNormalizer::normalize('foo', RequestInterface::METHOD_HEAD));
    }

    public function testNormalizeWithEmptyString()
    {
        $this->assertNull(BodyNormalizer::normalize('', RequestInterface::METHOD_GET));
    }

    public function testNormalizeWithFalseBoolean()
    {
        $this->assertNull(BodyNormalizer::normalize(false, RequestInterface::METHOD_GET));
    }
}
