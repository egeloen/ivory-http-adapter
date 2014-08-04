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
     * {@inhertdoc}
     */
    protected function doSend($url, $method, array $headers, $data, array $files)
    {
        $context = stream_context_create(array(
            'http' => array(
                'protocol_version' => $this->protocolVersion,
                'follow_location'  => $this->hasMaxRedirects(),
                'max_redirects'    => $this->maxRedirects + 1,
                'method'           => $this->prepareMethod($method),
                'header'           => $this->prepareHeaders($headers, $data, $files, false),
                'content'          => $this->prepareData($data, $files),
                'timeout'          => $this->timeout,
                'ignore_errors'    => !$this->hasMaxRedirects() && PHP_VERSION_ID === 50303,
            )
        ));

        list($body, $headers) = $this->process($this->prepareUrl($url), $context);

        if ($body === false) {
            throw HttpAdapterException::cannotFetchUrl($url, $this->getName(), print_r(error_get_last(), true));
        }

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

    /**
     * Processes the url/context.
     *
     * @param string   $url     The url.
     * @param resource $context The context.
     *
     * @return array The processed url/context (0 => body, 1 => headers).
     */
    abstract protected function process($url, $context);
}
