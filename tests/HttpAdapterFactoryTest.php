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

use Ivory\HttpAdapter\HttpAdapterFactory;

/**
 * Http adapter factory test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class HttpAdapterFactoryTest extends AbstractTestCase
{
    /**
     * @dataProvider httpAdapterProvider
     */
    public function testCapable($name)
    {
        $this->assertTrue(HttpAdapterFactory::capable($name));
    }

    public function testCapableWithInvalidName()
    {
        $this->assertFalse(HttpAdapterFactory::capable('foo'));
    }

    /**
     * @dataProvider httpAdapterProvider
     */
    public function testCreate($name, $class)
    {
        $this->assertInstanceOf($class, HttpAdapterFactory::create($name));
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     * @expectedExceptionMessage The http adapter "bar" does not exist.
     */
    public function testCreateWithInvalidName()
    {
        HttpAdapterFactory::create('bar');
    }

    public function testRegister()
    {
        HttpAdapterFactory::register(
            $name = 'foo',
            $class = $this->getMockClass('Ivory\HttpAdapter\HttpAdapterInterface')
        );

        $this->assertTrue(HttpAdapterFactory::capable($name));
        $this->assertInstanceOf($class, HttpAdapterFactory::create($name));
    }

    public function testRegisterWithClient()
    {
        HttpAdapterFactory::register(
            $name = 'foo',
            $class = $this->getMockClass('Ivory\HttpAdapter\HttpAdapterInterface'),
            'stdClass'
        );

        $this->assertTrue(HttpAdapterFactory::capable($name));
        $this->assertInstanceOf($class, HttpAdapterFactory::create($name));
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     * @expectedExceptionMessage The http adapter "foo" is not usable.
     */
    public function testRegisterWithInvalidClient()
    {
        HttpAdapterFactory::register(
            $name = 'foo',
            $this->getMockClass('Ivory\HttpAdapter\HttpAdapterInterface'),
            'bar'
        );

        $this->assertFalse(HttpAdapterFactory::capable($name));
        HttpAdapterFactory::create($name);
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     * @expectedExceptionMessage The class "stdClass" must implement "Ivory\HttpAdapter\HttpAdapterInterface".
     */
    public function testRegisterWithInvalidClass()
    {
        HttpAdapterFactory::register('foo', 'stdClass');
    }

    public function testUnregister()
    {
        HttpAdapterFactory::register(
            $name = 'foo',
            $this->getMockClass('Ivory\HttpAdapter\HttpAdapterInterface')
        );

        HttpAdapterFactory::unregister($name);

        $this->assertFalse(HttpAdapterFactory::capable($name));
    }

    /**
     * @dataProvider guessProvider
     */
    public function testGuess($preferred, $class)
    {
        $this->assertInstanceOf($class, HttpAdapterFactory::guess($preferred));
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     * @expectedExceptionMessage No http adapters are usable.
     */
    public function testGuessWithoutAdapters()
    {
        $providers = array_merge(
            $this->httpAdapterProvider(),
            array(
                array(HttpAdapterFactory::CURL),
                array(HttpAdapterFactory::GUZZLE3),
                array(HttpAdapterFactory::HTTPFUL),
            )
        );

        foreach ($providers as $provider) {
            HttpAdapterFactory::unregister($provider[0]);
        }

        HttpAdapterFactory::guess();
    }

    /**
     * Gets the http adapter provider.
     *
     * @return array The http adapter provider.
     */
    public function httpAdapterProvider()
    {
        $adapters = array(
            array(HttpAdapterFactory::BUZZ, 'Ivory\HttpAdapter\BuzzHttpAdapter'),
            array(HttpAdapterFactory::CAKE, 'Ivory\HttpAdapter\CakeHttpAdapter'),
            array(HttpAdapterFactory::FILE_GET_CONTENTS, 'Ivory\HttpAdapter\FileGetContentsHttpAdapter'),
            array(HttpAdapterFactory::FOPEN, 'Ivory\HttpAdapter\FopenHttpAdapter'),
            array(HttpAdapterFactory::REQUESTS, 'Ivory\HttpAdapter\RequestsHttpAdapter'),
            array(HttpAdapterFactory::SOCKET, 'Ivory\HttpAdapter\SocketHttpAdapter'),
            array(HttpAdapterFactory::ZEND1, 'Ivory\HttpAdapter\Zend1HttpAdapter'),
        );

        if (function_exists('curl_init')) {
            $adapters[] = array(HttpAdapterFactory::CURL, 'Ivory\HttpAdapter\CurlHttpAdapter');
            $adapters[] = array(HttpAdapterFactory::HTTPFUL, 'Ivory\HttpAdapter\HttpfulHttpAdapter');

            if (class_exists('Guzzle\Common\Version')) {
                $adapters[] = array(HttpAdapterFactory::GUZZLE3, 'Ivory\HttpAdapter\Guzzle3HttpAdapter');
            }
        }

        if (class_exists('GuzzleHttp\Adapter\Curl\CurlAdapter')) {
            $adapters[] = array(HttpAdapterFactory::GUZZLE4, 'Ivory\HttpAdapter\Guzzle4HttpAdapter');
        }

        if (class_exists('GuzzleHttp\Ring\Client\CurlHandler')) {
            $adapters[] = array(HttpAdapterFactory::GUZZLE5, 'Ivory\HttpAdapter\Guzzle5HttpAdapter');
        }

        if (class_exists('GuzzleHttp\Handler\CurlHandler')) {
            $adapters[] = array(HttpAdapterFactory::GUZZLE6, 'Ivory\HttpAdapter\Guzzle6HttpAdapter');
        }

        if (class_exists('http\Client')) {
            $adapters[] = array(HttpAdapterFactory::PECL_HTTP, 'Ivory\HttpAdapter\PeclHttpAdapter');
        }

        if (class_exists('React\HttpClient\Factory')) {
            $adapters[] = array(HttpAdapterFactory::REACT, 'Ivory\HttpAdapter\ReactHttpAdapter');
        }

        if (class_exists('Zend\Http\Client')) {
            $adapters[] = array(HttpAdapterFactory::ZEND2, 'Ivory\HttpAdapter\Zend2HttpAdapter');
        }

        return $adapters;
    }

    /**
     * Gets the guess provider.
     *
     * @return array The guess provider.
     */
    public function guessProvider()
    {
        if (class_exists('GuzzleHttp\Handler\CurlHandler')) {
            $httpAdapter = 'Ivory\HttpAdapter\Guzzle6HttpAdapter';
        } elseif (class_exists('GuzzleHttp\Ring\Client\CurlHandler')) {
            $httpAdapter = 'Ivory\HttpAdapter\Guzzle5HttpAdapter';
        } elseif (class_exists('GuzzleHttp\Adapter\Curl\CurlAdapter')) {
            $httpAdapter = 'Ivory\HttpAdapter\Guzzle4HttpAdapter';
        } elseif (function_exists('curl_init')) {
            $httpAdapter = 'Ivory\HttpAdapter\Guzzle3HttpAdapter';
        } elseif (class_exists('Zend\Http\Client')) {
            $httpAdapter = 'Ivory\HttpAdapter\Zend2HttpAdapter';
        } else {
            $httpAdapter = 'Ivory\HttpAdapter\Zend1HttpAdapter';
        }

        return array_merge(
            $this->httpAdapterProvider(),
            array(
                array(array(), $httpAdapter),
                array('foo', $httpAdapter),
                array(array('foo', HttpAdapterFactory::SOCKET), 'Ivory\HttpAdapter\SocketHttpAdapter'),
            )
        );
    }
}
