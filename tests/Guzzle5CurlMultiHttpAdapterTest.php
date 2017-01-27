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

use GuzzleHttp\Ring\Client\CurlMultiHandler;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Guzzle5CurlMultiHttpAdapterTest extends AbstractGuzzle5CurlHttpAdapterTest
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
    protected function createHandler()
    {
        return new CurlMultiHandler();
    }
}
