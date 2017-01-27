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
use Psr\Http\Message\RequestInterface;

/**
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
     * @return ConfigurationInterface
     */
    public function getConfiguration();

    /**
     * @param ConfigurationInterface $configuration
     */
    public function setConfiguration(ConfigurationInterface $configuration);

    /**
     * @param RequestInterface $request
     *
     * @throws HttpAdapterException
     *
     * @return ResponseInterface
     */
    public function sendRequest(RequestInterface $request);

    /**
     * @param array $requests
     *
     * @throws MultiHttpAdapterException
     *
     * @return array $responses
     */
    public function sendRequests(array $requests);

    /**
     * @return string
     */
    public function getName();
}
