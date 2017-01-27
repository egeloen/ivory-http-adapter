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

use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface HttpAdapterInterface extends PsrHttpAdapterInterface
{
    /**
     * @param string|object $uri
     * @param array         $headers
     *
     * @throws HttpAdapterException
     *
     * @return ResponseInterface
     */
    public function get($uri, array $headers = []);

    /**
     * @param string|object $uri
     * @param array         $headers
     *
     * @throws HttpAdapterException
     *
     * @return ResponseInterface
     */
    public function head($uri, array $headers = []);

    /**
     * @param string|object $uri
     * @param array         $headers
     *
     * @throws HttpAdapterException
     *
     * @return ResponseInterface
     */
    public function trace($uri, array $headers = []);

    /**
     * @param string|object $uri
     * @param array         $headers
     * @param array|string  $datas
     * @param array         $files
     *
     * @throws HttpAdapterException
     *
     * @return ResponseInterface
     */
    public function post($uri, array $headers = [], $datas = [], array $files = []);

    /**
     * @param string|object $uri
     * @param array         $headers
     * @param array|string  $datas
     * @param array         $files
     *
     * @throws HttpAdapterException
     *
     * @return ResponseInterface
     */
    public function put($uri, array $headers = [], $datas = [], array $files = []);

    /**
     * @param string|object $uri
     * @param array         $headers
     * @param array|string  $datas
     * @param array         $files
     *
     * @throws HttpAdapterException
     *
     * @return ResponseInterface
     */
    public function patch($uri, array $headers = [], $datas = [], array $files = []);

    /**
     * @param string|object $uri
     * @param array         $headers
     * @param array|string  $datas
     * @param array         $files
     *
     * @throws HttpAdapterException
     *
     * @return ResponseInterface
     */
    public function delete($uri, array $headers = [], $datas = [], array $files = []);

    /**
     * @param string|object $uri
     * @param array         $headers
     * @param array|string  $datas
     * @param array         $files
     *
     * @throws HttpAdapterException
     *
     * @return ResponseInterface
     */
    public function options($uri, array $headers = [], $datas = [], array $files = []);

    /**
     * @param string|object $uri
     * @param string        $method
     * @param array         $headers
     * @param array|string  $datas
     * @param array         $files
     *
     * @throws HttpAdapterException
     *
     * @return ResponseInterface
     */
    public function send($uri, $method, array $headers = [], $datas = [], array $files = []);
}
