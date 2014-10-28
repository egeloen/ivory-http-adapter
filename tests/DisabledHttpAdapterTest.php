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

use Buzz\Browser;
use Buzz\Client\Curl;
use Buzz\Client\MultiCurl;
use Ivory\HttpAdapter\BuzzHttpAdapter;
use Ivory\HttpAdapter\CurlHttpAdapter;
use Ivory\HttpAdapter\GuzzleHttpAdapter;
use Ivory\HttpAdapter\HttpfulHttpAdapter;

/**
 * Disabled http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class DisabledHttpAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testBuzzHttpAdapterWithMultiCurl()
    {
        new BuzzHttpAdapter(new Browser(new MultiCurl()));
    }

    public function testBuzzCurlHttpAdapterWithoutCurl()
    {
        if (function_exists('curl_init')) {
            $this->markTestSkipped();
        }

        $this->setExpectedException('\Ivory\HttpAdapter\HttpAdapterException');
        new BuzzHttpAdapter(new Browser(new Curl()));
    }

    public function testCurlHttpAdapterWithoutCurl()
    {
        if (function_exists('curl_init')) {
            $this->markTestSkipped();
        }

        $this->setExpectedException('\Ivory\HttpAdapter\HttpAdapterException');
        new CurlHttpAdapter();
    }

    public function testGuzzle3HttpAdapterWithoutCurl()
    {
        if (function_exists('curl_init')) {
            $this->markTestSkipped();
        }

        $this->setExpectedException('\Ivory\HttpAdapter\HttpAdapterException');
        new GuzzleHttpAdapter();
    }

    public function testHttpfulHttpAdapterWithoutCurl()
    {
        if (function_exists('curl_init')) {
            $this->markTestSkipped();
        }

        $this->setExpectedException('\Ivory\HttpAdapter\HttpAdapterException');
        new HttpfulHttpAdapter();
    }
}
