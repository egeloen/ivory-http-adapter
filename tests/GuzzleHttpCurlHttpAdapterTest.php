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

use GuzzleHttp\Adapter\Curl\CurlAdapter;
use GuzzleHttp\Ring\Client\CurlHandler;

/**
 * Guzzle http curl http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class GuzzleHttpCurlHttpAdapterTest extends AbstractGuzzleHttpCurlHttpAdapterTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (PHP_VERSION_ID < 50500) {
            $this->markTestSkipped();
        }

        parent::setUp();
    }

    /**
     * {@inheritdoc}
     */
    protected function createAdapter()
    {
        if (class_exists('GuzzleHttp\Ring\Client\CurlHandler')) {
            return new CurlHandler();
        }

        return new CurlAdapter($this->createMessageFactory());
    }
}
