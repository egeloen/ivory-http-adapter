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
 * Requests curl http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class RequestsCurlHttpAdapterTest extends AbstractRequestsHttpAdapterTest
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
    protected function createTransport()
    {
        return new \Requests_Transport_cURL();
    }
}
