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

use Zend\Http\Client\Adapter\Curl;

/**
 * Zend 2 curl http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Zend2CurlHttpAdapterTest extends AbstractZend2HttpAdapterTest
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

    /**
     * {@inheritdoc}
     */
    protected function createAdapter()
    {
        return new Curl();
    }
}
