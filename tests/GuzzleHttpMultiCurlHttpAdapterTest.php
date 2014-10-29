<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter;

use GuzzleHttp\Adapter\Curl\MultiAdapter;
use GuzzleHttp\Ring\Client\CurlMultiHandler;

/**
 * Guzzle http multi curl http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class GuzzleHttpMultiCurlHttpAdapterTest extends AbstractGuzzleHttpCurlHttpAdapterTest
{
    /**
     * {@inheritdoc}
     */
    protected function createAdapter()
    {
        if (class_exists('GuzzleHttp\Ring\Client\CurlMultiHandler')) {
            return new CurlMultiHandler();
        }

        return new MultiAdapter($this->createMessageFactory());
    }
}
