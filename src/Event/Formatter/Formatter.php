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
        return [
            'protocol_version' => $request->getProtocolVersion(),
            'uri'              => (string) $request->getUri(),
            'method'           => $request->getMethod(),
            'headers'          => $request->getHeaders(),
            'body'             => utf8_encode((string) $request->getBody()),
            'datas'            => $request->getDatas(),
            'files'            => $request->getFiles(),
            'parameters'       => $this->filterParameters($request->getParameters()),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function formatResponse(ResponseInterface $response)
    {
        return [
            'protocol_version' => $response->getProtocolVersion(),
            'status_code'      => $response->getStatusCode(),
            'reason_phrase'    => $response->getReasonPhrase(),
            'headers'          => $response->getHeaders(),
            'body'             => utf8_encode((string) $response->getBody()),
            'parameters'       => $this->filterParameters($response->getParameters()),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function formatException(HttpAdapterException $exception)
    {
        return [
            'code'    => $exception->getCode(),
            'message' => $exception->getMessage(),
            'line'    => $exception->getLine(),
            'file'    => $exception->getFile(),
        ];
    }

    /**
     * Filters the parameters.
     *
     * @param array $parameters The parameters.
     *
     * @return array The filtered parameters.
     */
    private function filterParameters(array $parameters)
    {
        return array_filter($parameters, function ($parameter) {
            return !is_object($parameter) && !is_resource($parameter);
        });
    }
}
