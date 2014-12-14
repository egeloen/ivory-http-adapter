<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\Formatter;

use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\HttpAdapterInterface;

/**
 * Formatter
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface FormatterInterface
{
    /**
     * Formats an http adapter.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface $httpAdapter The http adapter.
     */
    public function formatHttpAdapter(HttpAdapterInterface $httpAdapter);

    /**
     * Formats the request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request The request.
     *
     * @return array The formatted request.
     */
    public function formatRequest(InternalRequestInterface $request);

    /**
     * Formats the response.
     *
     * @param \Ivory\HttpAdapter\Message\ResponseInterface $response The response.
     *
     * @return array The formatted response.
     */
    public function formatResponse(ResponseInterface $response);

    /**
     * Formats the exception.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterException $exception The exception.
     *
     * @return array The formatted exception.
     */
    public function formatException(HttpAdapterException $exception);
}
