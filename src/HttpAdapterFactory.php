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
    private static $classes = array(
        self::BUZZ              => 'Ivory\HttpAdapter\BuzzHttpAdapter',
        self::CAKE              => 'Ivory\HttpAdapter\CakeHttpAdapter',
        self::CURL              => 'Ivory\HttpAdapter\CurlHttpAdapter',
        self::FILE_GET_CONTENTS => 'Ivory\HttpAdapter\FileGetContentsHttpAdapter',
        self::FOPEN             => 'Ivory\HttpAdapter\FopenHttpAdapter',
        self::GUZZLE            => 'Ivory\HttpAdapter\GuzzleHttpAdapter',
        self::GUZZLE_HTTP       => 'Ivory\HttpAdapter\GuzzleHttpHttpAdapter',
        self::HTTPFUL           => 'Ivory\HttpAdapter\HttpfulHttpAdapter',
        self::REACT             => 'Ivory\HttpAdapter\ReactHttpAdapter',
        self::SOCKET            => 'Ivory\HttpAdapter\SocketHttpAdapter',
        self::ZEND1             => 'Ivory\HttpAdapter\Zend1HttpAdapter',
        self::ZEND2             => 'Ivory\HttpAdapter\Zend2HttpAdapter',
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
        if (!isset(self::$classes[$name])) {
            throw HttpAdapterException::httpAdapterDoesNotExist($name);
        }

        return new self::$classes[$name]();
    }

    /**
     * Registers an http adapter.
     *
     * @param string $name  The name.
     * @param string $class The class.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the class is not an http adapter.
     */
    public static function register($name, $class)
    {
        if (!in_array('Ivory\HttpAdapter\HttpAdapterInterface', class_implements($class), true)) {
            throw HttpAdapterException::httpAdapterMustImplementInterface($class);
        }

        self::$classes[$name] = $class;
    }
}
