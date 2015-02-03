<?php

/**
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter;

use Ivory\HttpAdapter\HttpAdapterFactory;
use Ivory\Tests\HttpAdapter\Utility\CakeUtility;

/**
 * Http adapter factory test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class HttpAdapterFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass()
    {
        CakeUtility::setUp();
    }

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
                array(HttpAdapterFactory::GUZZLE),
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
            array(HttpAdapterFactory::SOCKET, 'Ivory\HttpAdapter\SocketHttpAdapter'),
            array(HttpAdapterFactory::ZEND1, 'Ivory\HttpAdapter\Zend1HttpAdapter'),
        );

        if (function_exists('curl_init')) {
            $adapters[] = array(HttpAdapterFactory::CURL, 'Ivory\HttpAdapter\CurlHttpAdapter');
            $adapters[] = array(HttpAdapterFactory::GUZZLE, 'Ivory\HttpAdapter\GuzzleHttpAdapter');
            $adapters[] = array(HttpAdapterFactory::HTTPFUL, 'Ivory\HttpAdapter\HttpfulHttpAdapter');
        }

        if (class_exists('GuzzleHttp\Client')) {
            $adapters[] = array(HttpAdapterFactory::GUZZLE_HTTP, 'Ivory\HttpAdapter\GuzzleHttpHttpAdapter');
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
        if (class_exists('GuzzleHttp\Client')) {
            $httpAdapter = 'Ivory\HttpAdapter\GuzzleHttpHttpAdapter';
        } elseif (function_exists('curl_init')) {
            $httpAdapter = 'Ivory\HttpAdapter\GuzzleHttpAdapter';
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
