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

/**
 * Abstract guzzle http curl http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractGuzzleHttpCurlHttpAdapterTest extends AbstractGuzzleHttpHttpAdapterTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!function_exists('curl_init')) {
            $this->markTestSkipped();
        }

        parent::setUp();
    }
}
