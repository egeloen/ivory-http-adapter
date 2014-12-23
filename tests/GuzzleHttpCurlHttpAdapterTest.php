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
use GuzzleHttp\Client;
use GuzzleHttp\Ring\Client\CurlHandler;
use Ivory\HttpAdapter\GuzzleHttpHttpAdapter;
use Ivory\Tests\HttpAdapter\Utility\PHPUnitUtility;

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

    /**
     * Tests a relative path when a base URL has been set in the GuzzleHttp client
     *
     * @dataProvider simplePlusRelativeProvider
     *
     * @param string $url
     * @param array $headers
     */
    public function testGetWithBaseUrl($url, array $headers = array())
    {
        if (!($baseUrl = PHPUnitUtility::getUrl())) {
            $this->markTestSkipped();
        }

        $http = new GuzzleHttpHttpAdapter(
            new Client(
                array(
                    'base_url' => $baseUrl,
                    'adapter' => $this->createAdapter()
                )
            )
        );

        $this->assertResponse($http->get($url, $headers));
    }

    /**
     * Gets the request provider.
     *
     * @return array The request provider.
     */
    public function simplePlusRelativeProvider()
    {
        return array_merge(
            $this->simpleProvider(),
            array(
                array('/'),
            )
        );
    }
}
