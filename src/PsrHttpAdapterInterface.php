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
 * PSR http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface PsrHttpAdapterInterface
{
    const VERSION = '0.8.0-DEV';
    const VERSION_ID = '00800';
    const MAJOR_VERSION = '0';
    const MINOR_VERSION = '8';
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
     * Sends PSR requests.
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
