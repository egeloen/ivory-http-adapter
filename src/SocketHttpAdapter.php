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

use Ivory\HttpAdapter\Extractor\ProtocolVersionExtractor;
use Ivory\HttpAdapter\Extractor\StatusCodeExtractor;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Normalizer\BodyNormalizer;
use Ivory\HttpAdapter\Normalizer\HeadersNormalizer;

/**
 * Socket http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class SocketHttpAdapter extends AbstractHttpAdapter
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'socket';
    }

    /**
     * {@inheritdoc}
     */
    protected function sendInternalRequest(InternalRequestInterface $internalRequest)
    {
        $uri = $internalRequest->getUri();

        $socket = @stream_socket_client(
            ($uri->getScheme() === 'https' ? 'ssl' : 'tcp').'://'.$uri->getHost().':'.($uri->getPort() ?: 80),
            $errno,
            $errstr,
            $this->getConfiguration()->getTimeout()
        );

        if ($socket === false) {
            throw HttpAdapterException::cannotFetchUri($uri, $this->getName(), $errstr);
        }

        stream_set_timeout($socket, $this->getConfiguration()->getTimeout());
        fwrite($socket, $this->prepareRequest($internalRequest));
        list($responseHeaders, $body) = $this->parseResponse($socket);
        $hasTimeout = $this->detectTimeout($socket);
        fclose($socket);

        if ($hasTimeout) {
            throw HttpAdapterException::timeoutExceeded(
                $uri,
                $this->getConfiguration()->getTimeout(),
                $this->getName()
            );
        }

        return $this->getConfiguration()->getMessageFactory()->createResponse(
            StatusCodeExtractor::extract($responseHeaders),
            ProtocolVersionExtractor::extract($responseHeaders),
            $responseHeaders = HeadersNormalizer::normalize($responseHeaders),
            BodyNormalizer::normalize($this->decodeBody($responseHeaders, $body), $internalRequest->getMethod())
        );
    }

    /**
     * Prepares the request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     *
     * @return string The prepared request.
     */
    private function prepareRequest(InternalRequestInterface $internalRequest)
    {
        $uri = $internalRequest->getUri();
        $path = $uri->getPath().($uri->getQuery() ? '?'.$uri->getQuery() : '');

        $request = $internalRequest->getMethod().' '.$path.' HTTP/'.$internalRequest->getProtocolVersion()."\r\n";
        $request .= 'Host: '.$uri->getHost().($uri->getPort() !== null ? ':'.$uri->getPort() : '')."\r\n";
        $request .= implode("\r\n", $this->prepareHeaders($internalRequest, false, true, true))."\r\n\r\n";
        $request .= $this->prepareBody($internalRequest)."\r\n";

        return $request;
    }

    /**
     * Parses the response.
     *
     * @param resource $socket The socket.
     *
     * @return array The response (0 => headers, 1 => body).
     */
    private function parseResponse($socket)
    {
        $headers = '';
        $body = '';
        $processHeaders = true;

        while (!feof($socket) && !$this->detectTimeout($socket)) {
            $line = fgets($socket);

            if ($line === "\r\n") {
                $processHeaders = false;
            } elseif ($processHeaders) {
                $headers .= $line;
            } else {
                $body .= $line;
            }
        }

        return array($headers, $body);
    }

    /**
     * Decodes the body.
     *
     * @param array  $headers The headers.
     * @param string $body    The body.
     *
     * @return string The decoded body.
     */
    private function decodeBody(array $headers, $body)
    {
        $headers = array_change_key_case($headers);

        if (isset($headers['transfer-encoding']) && $headers['transfer-encoding'] === 'chunked') {
            for ($decodedBody = ''; !empty($body); $body = trim($body)) {
                $pos = strpos($body, "\r\n");
                $length = hexdec(substr($body, 0, $pos));
                $decodedBody .= substr($body, $pos + 2, $length);
                $body = substr($body, $pos + $length + 2);
            }

            return $decodedBody;
        }

        return $body;
    }

    /**
     * Detects a timeout.
     *
     * @param resource $socket The socket.
     *
     * @return boolean TRUE if the socket has timeout else FALSE.
     */
    private function detectTimeout($socket)
    {
        $info = stream_get_meta_data($socket);

        return $info['timed_out'];
    }
}
