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
class HttpAdapterFactoryTest extends \PHPUnit_Framework_TestCase
{
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

        $this->assertInstanceOf($class, HttpAdapterFactory::create($name));
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     * @expectedExceptionMessage The class "stdClass" must implement "Ivory\HttpAdapter\HttpAdapterInterface".
     */
    public function testRegisterWithInvalidClass()
    {
        HttpAdapterFactory::register('foo', 'stdClass');
    }

    public function testGuess()
    {
        $adapter = HttpAdapterFactory::guess(HttpAdapterFactory::SOCKET);

        $this->assertInstanceOf('Ivory\HttpAdapter\HttpAdapterInterface', $adapter);
        $this->assertInstanceOf('Ivory\HttpAdapter\SocketHttpAdapter', $adapter);
    }

    /**
     * Gets the http adapter provider.
     *
     * @return array The http adapter provider.
     */
    public function httpAdapterProvider()
    {
        $adapters = array(
            array('buzz', 'Ivory\HttpAdapter\BuzzHttpAdapter'),
            array('cake', 'Ivory\HttpAdapter\CakeHttpAdapter'),
            array('file_get_contents', 'Ivory\HttpAdapter\FileGetContentsHttpAdapter'),
            array('fopen', 'Ivory\HttpAdapter\FopenHttpAdapter'),
            array('socket', 'Ivory\HttpAdapter\SocketHttpAdapter'),
            array('zend1', 'Ivory\HttpAdapter\Zend1HttpAdapter'),
        );

        if (function_exists('curl_init')) {
            $adapters[] = array('curl', 'Ivory\HttpAdapter\CurlHttpAdapter');
            $adapters[] = array('guzzle', 'Ivory\HttpAdapter\GuzzleHttpAdapter');
            $adapters[] = array('httpful', 'Ivory\HttpAdapter\HttpfulHttpAdapter');
        }

        if (class_exists('GuzzleHttp\Client')) {
            $adapters[] = array('guzzle_http', 'Ivory\HttpAdapter\GuzzleHttpHttpAdapter');
        }

        if (class_exists('React\HttpClient\Factory')) {
            $adapters[] = array('react', 'Ivory\HttpAdapter\ReactHttpAdapter');
        }

        if (class_exists('Zend\Http\Client')) {
            $adapters[] = array('zend2', 'Ivory\HttpAdapter\Zend2HttpAdapter');
        }

        return $adapters;
    }
}
