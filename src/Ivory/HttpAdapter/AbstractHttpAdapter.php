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

use Ivory\HttpAdapter\Message\MessageFactory;
use Ivory\HttpAdapter\Message\MessageFactoryInterface;
use Ivory\HttpAdapter\Message\MessageInterface;
use Ivory\HttpAdapter\Message\RequestInterface;
use Ivory\HttpAdapter\Message\Stream\ResourceStream;
use Ivory\HttpAdapter\Message\Stream\StringStream;

/**
 * Abstract http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractHttpAdapter implements HttpAdapterInterface
{
    /** @var \Ivory\HttpAdapter\Message\MessageFactoryInterface */
    protected $messageFactory;

    /** @var string */
    protected $protocolVersion = MessageInterface::PROTOCOL_VERSION_11;

    /** @var boolean */
    protected $keepAlive = false;

    /** @var string|null */
    protected $encodingType;

    /** @var string */
    protected $boundary;

    /** @var float */
    protected $timeout = 10;

    /** @var integer */
    protected $maxRedirects = 5;

    /**
     * Creates an http adapter.
     *
     * @param \Ivory\HttpAdapter\Message\MessageFactoryInterface|null $messageFactory The message factory.
     */
    public function __construct(MessageFactoryInterface $messageFactory = null)
    {
        $this->setMessageFactory($messageFactory ?: new MessageFactory());
        $this->setBoundary(sha1(microtime()));
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageFactory()
    {
        return $this->messageFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function setMessageFactory(MessageFactoryInterface $factory)
    {
        $this->messageFactory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function setProtocolVersion($protocolVersion)
    {
        $this->protocolVersion = $protocolVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function getKeepAlive()
    {
        return $this->keepAlive;
    }

    /**
     * {@inheritdoc}
     */
    public function setKeepAlive($keepAlive)
    {
        $this->keepAlive = $keepAlive;
    }

    /**
     * {@inheritdoc}
     */
    public function hasEncodingType()
    {
        return $this->encodingType !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getEncodingType()
    {
        return $this->encodingType;
    }

    /**
     * {@inheritdoc}
     */
    public function setEncodingType($encodingType)
    {
        $this->encodingType = $encodingType;
    }

    /**
     * {@inheritdoc}
     */
    public function getBoundary()
    {
        return $this->boundary;
    }

    /**
     * {@inheritdoc}
     */
    public function setBoundary($boundary)
    {
        $this->boundary = $boundary;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * {@inheritdoc}
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMaxRedirects()
    {
        return $this->maxRedirects > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxRedirects()
    {
        return $this->maxRedirects;
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxRedirects($maxRedirects)
    {
        $this->maxRedirects = $maxRedirects;
    }

    /**
     * {@inheritdoc}
     */
    public function get($url, array $headers = array())
    {
        return $this->send($url, RequestInterface::METHOD_GET, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function head($url, array $headers = array())
    {
        return $this->send($url, RequestInterface::METHOD_HEAD, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function post($url, array $headers = array(), $data = array(), array $files = array())
    {
        return $this->send($url, RequestInterface::METHOD_POST, $headers, $data, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function put($url, array $headers = array(), $data = array(), array $files = array())
    {
        return $this->send($url, RequestInterface::METHOD_PUT, $headers, $data, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function patch($url, array $headers = array(), $data = array(), array $files = array())
    {
        return $this->send($url, RequestInterface::METHOD_PATCH, $headers, $data, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($url, array $headers = array(), $data = array(), array $files = array())
    {
        return $this->send($url, RequestInterface::METHOD_DELETE, $headers, $data, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function options($url, array $headers = array(), $data = array(), array $files = array())
    {
        return $this->send($url, RequestInterface::METHOD_OPTIONS, $headers, $data, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function send($url, $method, array $headers = array(), $data = array(), array $files = array())
    {
        if (is_string($data) && !empty($files)) {
            throw HttpAdapterException::doesNotSupportDataAsStringAndFiles($this->getName());
        }

        return $this->doSend($url, $method, $headers, $data, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function sendRequest(RequestInterface $request)
    {
        $protocolVersion = $this->protocolVersion;
        $this->protocolVersion = $request->getProtocolVersion();

        $response = $this->send(
            (string) $request->getUrl(),
            $request->getMethod(),
            $request->getHeaders(),
            (string) $request->getBody()
        );

        $this->protocolVersion = $protocolVersion;

        return $response;
    }

    /**
     * Does a request send.
     *
     * @param string       $url     The url.
     * @param string       $method  The method.
     * @param array        $headers The headers.
     * @param array|string $data    The data.
     * @param array        $files   The files.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    abstract protected function doSend($url, $method, array $headers, $data, array $files);

    /**
     * Prepares the url.
     *
     * @param string $url The url.
     *
     * @return string The prepared url.
     */
    protected function prepareUrl($url)
    {
        return (strpos($url, 'http://') !== 0) && (strpos($url, 'https://') !== 0) ? 'http://'.$url : $url;
    }

    /**
     * Prepares the method.
     *
     * @param string $method The method.
     *
     * @return string The prepared method.
     */
    protected function prepareMethod($method)
    {
        return strtoupper($method);
    }

    /**
     * Prepares the headers.
     *
     * @param array        $headers     The headers.
     * @param array|string $data        The data.
     * @param array        $files       The files.
     * @param boolean      $associative TRUE if the headers should be an associative array else FALSE.
     *
     * @return array The prepared headers.
     */
    protected function prepareHeaders(array $headers, $data = array(), array $files = array(), $associative = true)
    {
        $headers = $this->normalizeHeaders($headers);

        if (!isset($headers['connection'])) {
            $headers['connection'] = $this->keepAlive ? 'keep-alive' : 'close';
        }

        if (!isset($headers['content-type'])) {
            if ($this->hasEncodingType()) {
                $headers['content-type'] = $this->encodingType;
            } elseif (!empty($files)) {
                $headers['content-type'] = self::ENCODING_TYPE_FORMDATA.'; boundary='.$this->boundary;
            } elseif (!empty($data)) {
                $headers['content-type'] = self::ENCODING_TYPE_URLENCODED;
            }
        }

        return $this->normalizeHeaders($headers, $associative);
    }

    /**
     * Prepares the data.
     *
     * @param array|string $data  The data.
     * @param array        $files The files.
     *
     * @return string The prepared data.
     */
    protected function prepareData($data, array $files = array())
    {
        if (empty($files)) {
            return is_array($data) ? http_build_query($data) : $data;
        }

        $rawData = '';

        foreach ($data as $name => $value) {
            $rawData .= $this->prepareRawData($name, $value);
        }

        foreach ($files as $name => $file) {
            $rawData .= $this->prepareRawData($name, file_get_contents($file), basename($file));
        }

        $rawData .= '--'.$this->boundary.'--'."\r\n";

        return $rawData;
    }

    /**
     * Prepares the raw data.
     *
     * @param string      $name     The name.
     * @param string      $value    The value.
     * @param string|null $filename The filename.
     *
     * @return string The prepared raw data.
     */
    protected function prepareRawData($name, $value, $filename = null)
    {
        $data = '--'.$this->boundary."\r\n".'Content-Disposition: form-data; name="'.$name.'"';

        if ($filename !== null) {
            $data .= '; filename="'.$filename.'"';
        }

        return $data."\r\n\r\n".$value."\r\n";
    }

    /**
     * Parses the protocol version.
     *
     * @param array|string $headers The headers.
     *
     * @return string The parsed protocol version.
     */
    protected function parseProtocolVersion($headers)
    {
        return substr($this->parseStatusLine($headers), 5, 3);
    }

    /**
     * Parses the status code.
     *
     * @param array|string $headers The headers.
     *
     * @return integer The parsed status code.
     */
    protected function parseStatusCode($headers)
    {
        return (integer) substr($this->parseStatusLine($headers), 9, 3);
    }

    /**
     * Parses the reason phrase.
     *
     * @param array|string $headers The headers.
     *
     * @return string The parsed reason phrase.
     */
    protected function parseReasonPhrase($headers)
    {
        return substr($this->parseStatusLine($headers), 13);
    }

    /**
     * Parses the status line.
     *
     * @param array|string $headers The headers.
     *
     * @return string The parsed status line.
     */
    protected function parseStatusLine($headers)
    {
        $headers = $this->parseHeaders($headers);

        return $headers[0];
    }

    /**
     * Parses the effective url.
     *
     * @param array|string $headers The headers.
     * @param string       $url     The url.
     *
     * @return string The parsed effective url.
     */
    protected function parseEffectiveUrl($headers, $url)
    {
        if (is_array($headers)) {
            $headers = implode(',', $this->normalizeHeaders($headers, false));
        }

        if ($this->hasMaxRedirects() && preg_match_all('/(L|l)ocation:([^,]+)/', $headers, $matches)) {
            return trim($matches[2][count($matches[2]) - 1]);
        }

        return $url;
    }

    /**
     * Parses the headers.
     *
     * @param array|string $headers The headers.
     *
     * @return array The parsed headers.
     */
    protected function parseHeaders($headers)
    {
        if (is_string($headers)) {
            $headers = explode("\r\n\r\n", trim($headers));

            return explode("\r\n", end($headers));
        }

        $parsedHeaders = array();

        foreach ($headers as $header) {
            if (($pos = strpos($header, ':')) === false) {
                $parsedHeaders = array($header);
            } else {
                $parsedHeaders[] = $header;
            }
        }

        return $parsedHeaders;
    }

    /**
     * Normalizes the headers.
     *
     * @param string|array $headers     The headers.
     * @param boolean      $associative TRUE if the headers should be an associative array else FALSE.
     *
     * @return array The normalized headers.
     */
    protected function normalizeHeaders($headers, $associative = true)
    {
        if (is_string($headers)) {
            $headers = $this->parseHeaders($headers);
        }

        $normalizedHeaders = array();

        foreach ($headers as $name => $value) {
            $value = $this->normalizeHeaderValue($value);

            if (!$associative) {
                $normalizedHeaders[] = is_int($name) ? $value : $name.': '.$value;
            } else {
                if (is_int($name)) {
                    if (($pos = strpos($value, ':')) === false) {
                        continue;
                    }

                    $name = substr($value, 0, $pos);
                    $value = substr($value, $pos + 1);
                }

                $normalizedHeaders[$this->normalizeHeaderName($name)] = $this->normalizeHeaderValue($value);
            }
        }

        return $normalizedHeaders;
    }

    /**
     * Normalizes the header name.
     *
     * @param string $name The header name.
     *
     * @return string The normalized header name.
     */
    protected function normalizeHeaderName($name)
    {
        return strtolower($name);
    }

    /**
     * Normalizes the header value.
     *
     * @param array|string $value The header value.
     *
     * @return string The normalized header value.
     */
    protected function normalizeHeaderValue($value)
    {
        return implode(', ', array_map('trim', (array) $value));
    }

    /**
     * Normalizes the body.
     *
     * @param mixed  $body   The body.
     * @param string $method The method.
     *
     * @return mixed The normalized body.
     */
    protected function normalizeBody($body, $method)
    {
        if ($method === RequestInterface::METHOD_HEAD || empty($body)) {
            return;
        }

        if (is_callable($body)) {
            return call_user_func($body);
        }

        return $body;
    }

    /**
     * Creates a response.
     *
     * @param string       $protocolVersion The protocol version.
     * @param integer      $statusCode      The status code.
     * @param string       $reasonPhrase    The reason phrase.
     * @param string       $method          The method.
     * @param string|array $headers         The headers.
     * @param mixed        $body            The body.
     * @param string       $effectiveUrl    The effective url.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The created response.
     */
    protected function createResponse(
        $protocolVersion,
        $statusCode,
        $reasonPhrase,
        $method,
        $headers,
        $body,
        $effectiveUrl
    ) {
        $response = $this->messageFactory->createResponse();
        $response->setProtocolVersion($protocolVersion);
        $response->setStatusCode($statusCode);
        $response->setReasonPhrase($reasonPhrase);
        $response->setHeaders($this->normalizeHeaders($headers));
        $response->setEffectiveUrl($effectiveUrl);

        $body = $this->normalizeBody($body, $method);

        if (is_resource($body)) {
            $response->setBody(new ResourceStream($body));
        } elseif (is_string($body)) {
            $response->setBody(new StringStream($body, StringStream::MODE_SEEK | StringStream::MODE_READ));
        } else {
            $response->setBody($body);
        }

        return $response;
    }
}
