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
use Ivory\HttpAdapter\Normalizer\BodyNormalizer;
use Ivory\HttpAdapter\Normalizer\HeadersNormalizer;
use Ivory\HttpAdapter\Parser\EffectiveUrlParser;
use Ivory\HttpAdapter\Parser\ProtocolVersionParser;
use Ivory\HttpAdapter\Parser\ReasonPhraseParser;
use Ivory\HttpAdapter\Parser\StatusCodeParser;

/**
 * Socket http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class SocketHttpAdapter extends AbstractHttpAdapter
{
    /** @var integer */
    protected $remainingRedirects;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        $this->remainingRedirects = $this->maxRedirects;
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxRedirects($maxRedirects)
    {
        parent::setMaxRedirects($maxRedirects);

        $this->remainingRedirects = $maxRedirects;
    }

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
    protected function doSend(InternalRequestInterface $internalRequest)
    {
        list($protocol, $host, $port, $path) = $this->parseUrl($internalRequest->getUrl());

        if (($socket = @stream_socket_client($protocol.'://'.$host.':'.$port, $errno, $errstr)) === false) {
            throw HttpAdapterException::cannotFetchUrl($internalRequest->getUrl(), $this->getName(), $errstr);
        }

        stream_set_timeout($socket, $this->timeout);
        fwrite($socket, $this->prepareRequest($internalRequest, $path, $host, $port));
        list($responseHeaders, $body) = $this->parseResponse($socket, $internalRequest->getUrl());
        $effectiveUrl = $this->parseEffectiveUrl($responseHeaders, $internalRequest->getUrl());

        if ($effectiveUrl !== $internalRequest->getUrl()) {
            $internalRequest->setUrl($effectiveUrl);

            return $this->doSend($internalRequest);
        }

        return $this->createResponse(
            ProtocolVersionParser::parse($responseHeaders),
            StatusCodeParser::parse($responseHeaders),
            ReasonPhraseParser::parse($responseHeaders),
            $responseHeaders = HeadersNormalizer::normalize($responseHeaders),
            BodyNormalizer::normalize($this->decodeBody($responseHeaders, $body), $internalRequest->getMethod()),
            $internalRequest->getUrl()
        );
    }

    /**
     * Prepares the request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     * @param string                                              $path            The path.
     * @param string                                              $host            The host.
     * @param integer                                             $port            The port.
     *
     * @return string The prepared request.
     */
    protected function prepareRequest(InternalRequestInterface $internalRequest, $path, $host, $port)
    {
        $body = $this->prepareBody($internalRequest);

        if (!$internalRequest->hasHeader('content-length') && ($contentLength = strlen($body)) > 0) {
            $internalRequest->setHeader('content-length', $contentLength);
        }

        $request = $internalRequest->getMethod().' '.$path.' HTTP/'.$internalRequest->getProtocolVersion()."\r\n";
        $request .= 'Host: '.$host.($port !== 80 ? ':'.$port : '')."\r\n";
        $request .= implode("\r\n", $this->prepareHeaders($internalRequest, false))."\r\n\r\n";
        $request .= $body."\r\n";

        return $request;
    }

    /**
     * Parses the response.
     *
     * @param resource $socket The socket.
     * @param string   $url    The url.
     *
     * @return array The response (0 => headers, 1 => body).
     */
    protected function parseResponse($socket, $url)
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

        if ($this->detectTimeout($socket)) {
            throw HttpAdapterException::timeoutExceeded($url, $this->timeout, $this->getName());
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
    protected function decodeBody(array $headers, $body)
    {
        $headers = array_change_key_case($headers);

        if (isset($headers['transfer-encoding']) && $headers['transfer-encoding'] === 'chunked') {
            for ($decodedBody = ''; !empty($body); $body = trim($body)) {
                $pos = strpos($body, "\r\n");
                $length = hexdec(substr($body, 0, $pos));
                $decodedBody.= substr($body, $pos + 2, $length);
                $body = substr($body, $pos + $length + 2);
            }

            return $decodedBody;
        }

        return $body;
    }

    /**
     * Parses the url.
     *
     * @param string $url The url.
     *
     * @return array The parsed url (0 => protocol, 1 => host, 2 => port, 3 => path).
     */
    protected function parseUrl($url)
    {
        $info = parse_url($url);

        return array(
            isset($info['scheme']) ? ($info['scheme'] === 'http' ? 'tcp': 'ssl') : 'tcp',
            $info['host'],
            isset($info['port']) ? $info['port'] : 80,
            sprintf(
                '%s%s',
                isset($info['path']) ? $info['path'] : '/',
                isset($info['query']) ? '?'.$info['query'] : ''
            )
        );
    }

    /**
     * Parses the effective url.
     *
     * @param string $headers The headers.
     * @param string $url     The url.
     *
     * @return string The parsed effective url.
     */
    protected function parseEffectiveUrl($headers, $url)
    {
        $effectiveUrl = EffectiveUrlParser::parse($headers, $url, $this->hasMaxRedirects());

        if ($effectiveUrl === $url) {
            $this->remainingRedirects = $this->maxRedirects;
        } elseif (--$this->remainingRedirects >= 0) {
            return $effectiveUrl;
        }

        if ($this->remainingRedirects < 0) {
            throw HttpAdapterException::maxRedirectsExceeded($url, $this->getName(), $this->maxRedirects);
        }

        return $url;
    }

    /**
     * Detects a timeout.
     *
     * @param resource $socket The socket.
     *
     * @return boolean TRUE if the socket has timeout else FALSE.
     */
    protected function detectTimeout($socket)
    {
        $info = stream_get_meta_data($socket);

        return $info['timed_out'];
    }
}
