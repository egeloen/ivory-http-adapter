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
use Ivory\HttpAdapter\Normalizer\MethodNormalizer;

/**
 * Method normalizer test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MethodNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalize()
    {
        $this->assertSame(RequestInterface::METHOD_GET, MethodNormalizer::normalize('get'));
    }
}
