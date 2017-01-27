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
 * @author GeLo <geloen.eric@gmail.com>
 */
class HttpAdapterFactoryTest extends AbstractTestCase
{
    /**
     * @param string $name
     *
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
     * @param string $name
     * @param string $class
     *
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
     * @param array|string $preferred
     * @param string       $class
     *
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
            [
                [HttpAdapterFactory::CURL],
                [HttpAdapterFactory::GUZZLE3],
                [HttpAdapterFactory::HTTPFUL],
            ]
        );

        foreach ($providers as $provider) {
            HttpAdapterFactory::unregister($provider[0]);
        }

        HttpAdapterFactory::guess();
    }

    /**
     * @return array
     */
    public function httpAdapterProvider()
    {
        $adapters = [
            [HttpAdapterFactory::BUZZ, 'Ivory\HttpAdapter\BuzzHttpAdapter'],
            [HttpAdapterFactory::CAKE, 'Ivory\HttpAdapter\CakeHttpAdapter'],
            [HttpAdapterFactory::FILE_GET_CONTENTS, 'Ivory\HttpAdapter\FileGetContentsHttpAdapter'],
            [HttpAdapterFactory::FOPEN, 'Ivory\HttpAdapter\FopenHttpAdapter'],
            [HttpAdapterFactory::REQUESTS, 'Ivory\HttpAdapter\RequestsHttpAdapter'],
            [HttpAdapterFactory::SOCKET, 'Ivory\HttpAdapter\SocketHttpAdapter'],
            [HttpAdapterFactory::ZEND1, 'Ivory\HttpAdapter\Zend1HttpAdapter'],
        ];

        if (function_exists('curl_init')) {
            $adapters[] = [HttpAdapterFactory::CURL, 'Ivory\HttpAdapter\CurlHttpAdapter'];
            $adapters[] = [HttpAdapterFactory::HTTPFUL, 'Ivory\HttpAdapter\HttpfulHttpAdapter'];

            if (class_exists('Guzzle\Common\Version')) {
                $adapters[] = [HttpAdapterFactory::GUZZLE3, 'Ivory\HttpAdapter\Guzzle3HttpAdapter'];
            }
        }

        if (class_exists('GuzzleHttp\Adapter\Curl\CurlAdapter')) {
            $adapters[] = [HttpAdapterFactory::GUZZLE4, 'Ivory\HttpAdapter\Guzzle4HttpAdapter'];
        }

        if (class_exists('GuzzleHttp\Ring\Client\CurlHandler')) {
            $adapters[] = [HttpAdapterFactory::GUZZLE5, 'Ivory\HttpAdapter\Guzzle5HttpAdapter'];
        }

        if (class_exists('GuzzleHttp\Handler\CurlHandler')) {
            $adapters[] = [HttpAdapterFactory::GUZZLE6, 'Ivory\HttpAdapter\Guzzle6HttpAdapter'];
        }

        if (class_exists('http\Client')) {
            $adapters[] = [HttpAdapterFactory::PECL_HTTP, 'Ivory\HttpAdapter\PeclHttpAdapter'];
        }

        if (class_exists('React\HttpClient\Factory')) {
            $adapters[] = [HttpAdapterFactory::REACT, 'Ivory\HttpAdapter\ReactHttpAdapter'];
        }

        if (class_exists('Zend\Http\Client')) {
            $adapters[] = [HttpAdapterFactory::ZEND2, 'Ivory\HttpAdapter\Zend2HttpAdapter'];
        }

        return $adapters;
    }

    /**
     * @return array
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
            [
                [[], $httpAdapter],
                ['foo', $httpAdapter],
                [['foo', HttpAdapterFactory::SOCKET], 'Ivory\HttpAdapter\SocketHttpAdapter'],
            ]
        );
    }
}
