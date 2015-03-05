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

use Psr\Http\Message\RequestInterface;

/**
 * Http adapter interface.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface HttpAdapterInterface
{
    const VERSION = '0.7.0-DEV';
    const VERSION_ID = '00600';
    const MAJOR_VERSION = '0';
    const MINOR_VERSION = '7';
    const PATCH_VERSION = '0';
    const EXTRA_VERSION = 'DEV';

    /**
     * Gets the configuration.
     *
     * @return \Ivory\HttpAdapter\ConfigurationInterface The configuration.
     */
    public function getConfiguration();

    /**
     * Sets the configuration.
     *
     * @param \Ivory\HttpAdapter\ConfigurationInterface $configuration The configuration.
     */
    public function setConfiguration(ConfigurationInterface $configuration);

    /**
     * Sends a GET request.
     *
     * @param string|object $uri     The uri.
     * @param array         $headers The headers.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function get($uri, array $headers = array());

    /**
     * Sends an HEAD request.
     *
     * @param string|object $uri     The uri.
     * @param array         $headers The headers.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function head($uri, array $headers = array());

    /**
     * Sends a TRACE request.
     *
     * @param string|object $uri     The uri.
     * @param array         $headers The headers.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function trace($uri, array $headers = array());

    /**
     * Sends a POST request.
     *
     * @param string|object $uri     The uri.
     * @param array         $headers The headers.
     * @param array|string  $datas   The datas.
     * @param array         $files   The files.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function post($uri, array $headers = array(), $datas = array(), array $files = array());

    /**
     * Sends a PUT request.
     *
     * @param string|object $uri     The uri.
     * @param array         $headers The headers.
     * @param array|string  $datas   The datas.
     * @param array         $files   The files.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function put($uri, array $headers = array(), $datas = array(), array $files = array());

    /**
     * Sends a PATCH request.
     *
     * @param string|object $uri     The uri.
     * @param array         $headers The headers.
     * @param array|string  $datas   The datas.
     * @param array         $files   The files.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function patch($uri, array $headers = array(), $datas = array(), array $files = array());

    /**
     * Sends a DELETE request.
     *
     * @param string|object $uri     The uri.
     * @param array         $headers The headers.
     * @param array|string  $datas   The datas.
     * @param array         $files   The files.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function delete($uri, array $headers = array(), $datas = array(), array $files = array());

    /**
     * Sends an OPTIONS request.
     *
     * @param string|object $uri     The uri.
     * @param array         $headers The headers.
     * @param array|string  $datas   The datas.
     * @param array         $files   The files.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function options($uri, array $headers = array(), $datas = array(), array $files = array());

    /**
     * Sends a request.
     *
     * @param string|object $uri     The uri.
     * @param string        $method  The method.
     * @param array         $headers The headers.
     * @param array|string  $datas   The datas.
     * @param array         $files   The files.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function send($uri, $method, array $headers = array(), $datas = array(), array $files = array());

    /**
     * Sends a PSR request.
     *
     * @param \Psr\Http\Message\RequestInterface $request The request.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function sendRequest(RequestInterface $request);

    /**
     * Sends requests.
     *
     * @param array $requests The requests.
     *
     * @throws \Ivory\HttpAdapter\MultiHttpAdapterException If an error occurred when you don't provide the error callable.
     *
     * @return array $responses The responses.
     */
    public function sendRequests(array $requests);

    /**
     * Gets the name.
     *
     * @return string The name.
     */
    public function getName();
}
