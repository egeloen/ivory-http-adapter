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
    const GUZZLE = 'guzzle';
    const GUZZLE_HTTP = 'guzzle_http';
    const HTTPFUL = 'httpful';
    const REACT = 'react';
    const SOCKET = 'socket';
    const ZEND1 = 'zend1';
    const ZEND2 = 'zend2';

    /** @var array */
    private static $adapters = array(
        self::GUZZLE_HTTP       => array('adapter' => 'Ivory\HttpAdapter\GuzzleHttpHttpAdapter',        'client' => '\GuzzleHttp\Client'),
        self::GUZZLE            => array('adapter' => 'Ivory\HttpAdapter\GuzzleHttpAdapter',            'client' => '\Guzzle\Http\Client'),
        self::BUZZ              => array('adapter' => 'Ivory\HttpAdapter\BuzzHttpAdapter',              'client' => '\Buzz\Browser'),
        self::ZEND1             => array('adapter' => 'Ivory\HttpAdapter\Zend1HttpAdapter',             'client' => '\Zend_Http_Client'),
        self::ZEND2             => array('adapter' => 'Ivory\HttpAdapter\Zend2HttpAdapter',             'client' => '\Zend\Http\Client'),
        self::CAKE              => array('adapter' => 'Ivory\HttpAdapter\CakeHttpAdapter',              'client' => '\HttpSocket'),
        self::REACT             => array('adapter' => 'Ivory\HttpAdapter\ReactHttpAdapter',             'client' => '\React\HttpClient\Request'),
        self::HTTPFUL           => array('adapter' => 'Ivory\HttpAdapter\HttpfulHttpAdapter',           'client' => '\Httpful\Request'),
        self::CURL              => array('adapter' => 'Ivory\HttpAdapter\CurlHttpAdapter',              'client' => 'curl_init'),
        self::FILE_GET_CONTENTS => array('adapter' => 'Ivory\HttpAdapter\FileGetContentsHttpAdapter',   'client' => 'allow_url_fopen'),
        self::FOPEN             => array('adapter' => 'Ivory\HttpAdapter\FopenHttpAdapter',             'client' => 'allow_url_fopen'),
        self::SOCKET            => array('adapter' => 'Ivory\HttpAdapter\SocketHttpAdapter',            'client' => 'stream_socket_client')
    );

    /**
     * Creates an http adapter.
     *
     * @param string $name The name.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the http adapter does not exist.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterInterface The http adapter.
     */
    public static function create($name)
    {
        if (!isset(self::$adapters[$name])) {
            throw HttpAdapterException::httpAdapterDoesNotExist($name);
        }
        if (!self::capable($name)) {
            throw HttpAdapterException::httpAdapterNotUsable($name);
        }

        return new self::$adapters[$name]['adapter']();
    }

    /**
     * Registers an http adapter.
     *
     * @param string $name  The name.
     * @param string $class The class.
     * @param string $client
     * @throws HttpAdapterException
     */
    public static function register($name, $class, $client = '\stdClass')
    {
        if (!in_array('Ivory\HttpAdapter\HttpAdapterInterface', class_implements($class), true)) {
            throw HttpAdapterException::httpAdapterMustImplementInterface($class);
        }

        self::$adapters[$name] = array('adapter' => $class, 'client'=> $client);
    }

    /**
     * guesses the best matching adapter
     *
     * @param  array $preferred
     * @return HttpAdapterInterface
     * @throws HttpAdapterException
     */
    public static function guess(array $preferred = array())
    {
        foreach ($preferred as $preference) {
            if (self::capable($preference)) {
                return self::create($preference);
            }
        }

        foreach (self::$adapters as $name => $data) {
            if (self::capable($name)) {
                return self::create($name);
            }
        }

        throw new HttpAdapterException('no suitable HTTP adapter found');
    }

    /**
     * checks if its possible to create a specified adapter
     *
     * @param string $name
     * @return bool
     */
    public static function capable($name)
    {
        return isset(self::$adapters[$name]) && (class_exists(self::$adapters[$name]['client']) || function_exists(self::$adapters[$name]['client']) || ini_get(self::$adapters[$name]['client']));
    }
}
