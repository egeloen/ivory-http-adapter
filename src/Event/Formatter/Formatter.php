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

use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * Formatter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Formatter implements FormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function formatRequest(InternalRequestInterface $request)
    {
        return array(
            'protocol_version' => $request->getProtocolVersion(),
            'uri'              => (string) $request->getUri(),
            'method'           => $request->getMethod(),
            'headers'          => $request->getHeaders(),
            'body'             => (string) $request->getBody(),
            'datas'            => $request->getDatas(),
            'files'            => $request->getFiles(),
            'parameters'       => $request->getParameters(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function formatResponse(ResponseInterface $response)
    {
        return array(
            'protocol_version' => $response->getProtocolVersion(),
            'status_code'      => $response->getStatusCode(),
            'reason_phrase'    => $response->getReasonPhrase(),
            'headers'          => $response->getHeaders(),
            'body'             => (string) $response->getBody(),
            'parameters'       => $response->getParameters(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function formatException(HttpAdapterException $exception)
    {
        return array(
            'code'    => $exception->getCode(),
            'message' => $exception->getMessage(),
            'line'    => $exception->getLine(),
            'file'    => $exception->getFile(),
        );
    }
}
