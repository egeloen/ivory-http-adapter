<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter;

/**
 * Http adapter factory.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class HttpAdapterFactory
{
    const BUZZ = 'buzz';
    const CAKE = 'cake';
    const CURL = 'curl';
    const FILE_GET_CONTENTS = 'file_get_contents';
    const FOPEN = 'fopen';
    const GUZZLE3 = 'guzzle3';
    const GUZZLE4 = 'guzzle4';
    const GUZZLE5 = 'guzzle5';
    const GUZZLE6 = 'guzzle6';
    const HTTPFUL = 'httpful';
    const PECL_HTTP = 'pecl_http';
    const REACT = 'react';
    const SOCKET = 'socket';
    const ZEND1 = 'zend1';
    const ZEND2 = 'zend2';

    /** @var array */
    private static $adapters = array(
        self::GUZZLE6 => array(
            'adapter' => 'Ivory\HttpAdapter\Guzzle6HttpAdapter',
            'client'  => 'GuzzleHttp\Handler\CurlHandler',
        ),
        self::GUZZLE5 => array(
            'adapter' => 'Ivory\HttpAdapter\Guzzle5HttpAdapter',
            'client'  => 'GuzzleHttp\Ring\Client\CurlHandler',
        ),
        self::GUZZLE4 => array(
            'adapter' => 'Ivory\HttpAdapter\Guzzle4HttpAdapter',
            'client'  => 'GuzzleHttp\Adapter\Curl\CurlAdapter',
        ),
        self::GUZZLE3 => array(
            'adapter' => 'Ivory\HttpAdapter\Guzzle3HttpAdapter',
            'client'  => 'Guzzle\Http\Client',
        ),
        self::ZEND2 => array(
            'adapter' => 'Ivory\HttpAdapter\Zend2HttpAdapter',
            'client'  => 'Zend\Http\Client',
        ),
        self::ZEND1 => array(
            'adapter' => 'Ivory\HttpAdapter\Zend1HttpAdapter',
            'client'  => 'Zend_Http_Client',
        ),
        self::BUZZ => array(
            'adapter' => 'Ivory\HttpAdapter\BuzzHttpAdapter',
            'client'  => 'Buzz\Browser',
        ),
        self::REACT => array(
            'adapter' => 'Ivory\HttpAdapter\ReactHttpAdapter',
            'client'  => 'React\HttpClient\Request',
        ),
        self::HTTPFUL => array(
            'adapter' => 'Ivory\HttpAdapter\HttpfulHttpAdapter',
            'client'  => 'Httpful\Request',
        ),
        self::PECL_HTTP => array(
            'adapter' => 'Ivory\HttpAdapter\PeclHttpAdapter',
            'client'  => 'http\Client',
        ),
        self::CAKE => array(
            'adapter' => 'Ivory\HttpAdapter\CakeHttpAdapter',
            'client'  => 'Cake\Network\Http\Client',
        ),
        self::CURL => array(
            'adapter' => 'Ivory\HttpAdapter\CurlHttpAdapter',
            'client'  => 'curl_init',
        ),
        self::FOPEN => array(
            'adapter' => 'Ivory\HttpAdapter\FopenHttpAdapter',
            'client'  => 'allow_url_fopen',
        ),
        self::FILE_GET_CONTENTS => array(
            'adapter' => 'Ivory\HttpAdapter\FileGetContentsHttpAdapter',
            'client'  => 'allow_url_fopen',
        ),
        self::SOCKET => array(
            'adapter' => 'Ivory\HttpAdapter\SocketHttpAdapter',
            'client'  => 'stream_socket_client',
        ),
    );

    /**
     * Registers an http adapter.
     *
     * @param string      $name   The name.
     * @param string      $class  The class.
     * @param string|null $client The client.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the class does not implement the http adapter interface.
     */
    public static function register($name, $class, $client = null)
    {
        if (!in_array('Ivory\HttpAdapter\HttpAdapterInterface', class_implements($class), true)) {
            throw HttpAdapterException::httpAdapterMustImplementInterface($class);
        }

        $adapter = array('adapter' => $class);

        if ($client !== null) {
            $adapter['client'] = $client;
        }

        self::unregister($name);
        self::$adapters = array_merge(array($name => $adapter), self::$adapters);
    }

    /**
     * Unregisters an adapter.
     *
     * @param string $name The name.
     */
    public static function unregister($name)
    {
        unset(self::$adapters[$name]);
    }

    /**
     * Checks if its possible to create an adapter.
     *
     * @param string $name The name.
     *
     * @return boolean TRUE if its possible to create the adapter else FALSE.
     */
    public static function capable($name)
    {
        return isset(self::$adapters[$name])
            && (!isset(self::$adapters[$name]['client'])
            || (class_exists(self::$adapters[$name]['client'])
            || function_exists(self::$adapters[$name]['client'])
            || ini_get(self::$adapters[$name]['client'])));
    }

    /**
     * Creates an http adapter.
     *
     * @param string $name The name.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the http adapter does not exist or is not usable.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterInterface The http adapter.
     */
    public static function create($name)
    {
        if (!isset(self::$adapters[$name])) {
            throw HttpAdapterException::httpAdapterDoesNotExist($name);
        }

        if (!self::capable($name)) {
            throw HttpAdapterException::httpAdapterIsNotUsable($name);
        }

        return new self::$adapters[$name]['adapter']();
    }

    /**
     * Guesses the best matching adapter.
     *
     * @param string|array $preferred The preferred adapters.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If no adapters can be guessed.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterInterface The guessed adapter.
     */
    public static function guess($preferred = array())
    {
        $adapters = self::$adapters;

        foreach ((array) $preferred as $preference) {
            if (self::capable($preference)) {
                return self::create($preference);
            }

            unset($adapters[$preference]);
        }

        foreach (array_keys($adapters) as $name) {
            if (self::capable($name)) {
                return self::create($name);
            }
        }

        throw HttpAdapterException::httpAdaptersAreNotUsable();
    }
}
