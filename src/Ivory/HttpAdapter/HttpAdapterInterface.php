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

use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\RequestInterface;

/**
 * Http adapter interface.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface HttpAdapterInterface extends HttpAdapterConfigInterface
{
    /**
     * Sends a GET request.
     *
     * @param string $url     The url.
     * @param array  $headers The headers.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function get($url, array $headers = array());

    /**
     * Sends an HEAD request.
     *
     * @param string $url     The url.
     * @param array  $headers The headers.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function head($url, array $headers = array());

    /**
     * Sends a POST request.
     *
     * @param string       $url     The url.
     * @param array        $headers The headers.
     * @param array|string $data    The data.
     * @param array        $files   The files.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function post($url, array $headers = array(), $data = array(), array $files = array());

    /**
     * Sends a PUT request.
     *
     * @param string       $url     The url.
     * @param array        $headers The headers.
     * @param array|string $data    The data.
     * @param array        $files   The files.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function put($url, array $headers = array(), $data = array(), array $files = array());

    /**
     * Sends a PATCH request.
     *
     * @param string       $url     The url.
     * @param array        $headers The headers.
     * @param array|string $data    The data.
     * @param array        $files   The files.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function patch($url, array $headers = array(), $data = array(), array $files = array());

    /**
     * Sends a DELETE request.
     *
     * @param string       $url     The url.
     * @param array        $headers The headers.
     * @param array|string $data    The data.
     * @param array        $files   The files.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function delete($url, array $headers= array(), $data = array(), array $files = array());

    /**
     * Sends an OPTIONS request.
     *
     * @param string       $url     The url.
     * @param array        $headers The headers.
     * @param array|string $data    The data.
     * @param array        $files   The files.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function options($url, array $headers= array(), $data = array(), array $files = array());

    /**
     * Sends a request.
     *
     * @param string       $url     The url.
     * @param string       $method  The method.
     * @param array        $headers The headers.
     * @param array|string $data    The data.
     * @param array        $files   The files.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function send($url, $method, array $headers = array(), $data = array(), array $files = array());

    /**
     * Sends a PSR request.
     *
     * @param \Ivory\HttpAdapter\Message\RequestInterface $request The request.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function sendRequest(RequestInterface $request);

    /**
     * Sends an internal request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function sendInternalRequest(InternalRequestInterface $internalRequest);
}
