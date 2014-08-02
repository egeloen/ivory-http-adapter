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
 * Abstract stream http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractStreamHttpAdapter extends AbstractHttpAdapter
{
    /**
     * Creates a context.
     *
     * @param string       $method  The method.
     * @param array        $headers The headers.
     * @param array|string $data    The data.
     * @param array        $files   The files.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If there are files.
     *
     * @return resource The created context.
     */
    protected function createContext($method, array $headers = array(), $data = array(), array $files = array())
    {
        $context = array(
            'http' => array(
                'protocol_version' => $this->protocolVersion,
                'follow_location'  => $this->hasMaxRedirects(),
                'max_redirects'    => $this->maxRedirects + 1,
                'method'           => $this->prepareMethod($method),
                'header'           => $this->prepareHeaders($headers, $data, $files, false),
                'content'          => $this->prepareData($data, $files),
                'ignore_errors'    => !$this->hasMaxRedirects() && PHP_VERSION_ID === 50303,
            )
        );

        return stream_context_create($context);
    }

    /**
     * Creates a stream response.
     *
     * @param string          $url     The url.
     * @param string          $method  The method.
     * @param array           $headers The headers.
     * @param resource|string $body    The body.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The created stream response.
     */
    protected function createStreamResponse($url, $method, array $headers, $body)
    {
        return $this->createResponse(
            $this->parseProtocolVersion($headers),
            $this->parseStatusCode($headers),
            $this->parseReasonPhrase($headers),
            $method,
            $headers,
            $body,
            $this->parseEffectiveUrl($headers, $url)
        );
    }
}
