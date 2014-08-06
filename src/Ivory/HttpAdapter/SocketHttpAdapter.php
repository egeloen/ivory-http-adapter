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

use Ivory\HttpAdapter\Message\MessageFactoryInterface;

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
    public function __construct(MessageFactoryInterface $messageFactory = null)
    {
        parent::__construct($messageFactory);

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
    protected function doSend($url, $method, array $headers, $data, array $files)
    {
        list($protocol, $host, $port, $path) = $this->parseUrl($url);

        if (($socket = @stream_socket_client($protocol.'://'.$host.':'.$port, $errno, $errstr)) === false) {
            throw HttpAdapterException::cannotFetchUrl($url, $this->getName(), $errstr);
        }

        stream_set_timeout($socket, $this->timeout);
        fwrite($socket, $this->prepareRequest($method, $path, $host, $port, $headers, $data, $files));
        list($statusLine, $responseHeaders, $body) = $this->parseResponse($socket, $url);

        if (($effectiveUrl = $this->parseEffectiveUrl($responseHeaders, $url)) !== $url) {
            return $this->doSend($effectiveUrl, $method, $headers, $data, $files);
        }

        return $this->createResponse(
            $this->parseProtocolVersion($statusLine),
            $this->parseStatusCode($statusLine),
            $this->parseReasonPhrase($statusLine),
            $method,
            $responseHeaders,
            $body,
            $url
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'socket';
    }

    /**
     * Prepares the request.
     *
     * @param string       $method  The method.
     * @param string       $path    The path.
     * @param string       $host    The host.
     * @param array        $headers The headers.
     * @param array|string $data    The data.
     * @param array        $files   The files.
     *
     * @return string The prepared request.
     */
    protected function prepareRequest($method, $path, $host, $port, array $headers, $data, array $files)
    {
        $headers = $this->prepareHeaders($headers, $data, $files);
        $data = $this->prepareData($data, $files);

        if (!isset($headers['content-length']) && ($contentLength = strlen($data)) > 0) {
            $headers['content-length'] = $contentLength;
        }

        $request = $this->prepareMethod($method).' '.$path.' HTTP/'.$this->protocolVersion."\r\n";
        $request .= 'Host: '.$host.($port !== 80 ? ':'.$port : '')."\r\n";
        $request .= implode("\r\n", $this->normalizeHeaders($headers, false))."\r\n\r\n";
        $request .= $data."\r\n";

        return $request;
    }

    /**
     * Parses the response.
     *
     * @param resource $socket The socket.
     * @param string   $url    The url.
     *
     * @return array The response (0 => status line, 1 => headers, 2 => body).
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

        $statusLine = $this->parseStatusLine($headers);
        $headers = $this->normalizeHeaders($headers);
        $body = $this->decodeBody($headers, $body);

        return array($statusLine, $headers, $body);
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
     * {@inheritdoc}
     */
    protected function parseEffectiveUrl($headers, $url)
    {
        $effectiveUrl = parent::parseEffectiveUrl($headers, $url);

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
