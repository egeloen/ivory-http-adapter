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

use Ivory\HttpAdapter\Event\Events;
use Ivory\HttpAdapter\Event\ExceptionEvent;
use Ivory\HttpAdapter\Event\PostSendEvent;
use Ivory\HttpAdapter\Event\PreSendEvent;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\MessageFactory;
use Ivory\HttpAdapter\Message\MessageFactoryInterface;
use Ivory\HttpAdapter\Message\MessageInterface;
use Ivory\HttpAdapter\Message\RequestInterface;
use Ivory\HttpAdapter\Message\Stream\ResourceStream;
use Ivory\HttpAdapter\Message\Stream\StringStream;
use Ivory\HttpAdapter\Normalizer\HeadersNormalizer;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Abstract http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractHttpAdapter implements HttpAdapterInterface
{
    /** @var \Ivory\HttpAdapter\Message\MessageFactoryInterface */
    protected $messageFactory;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

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
     */
    public function __construct()
    {
        $this->setMessageFactory(new MessageFactory());
        $this->setEventDispatcher(new EventDispatcher());
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
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
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
        $internalRequest = $this->messageFactory->createInternalRequest($url, $method);
        $internalRequest->setProtocolVersion($this->protocolVersion);
        $internalRequest->setHeaders($headers);
        $internalRequest->setData($data);
        $internalRequest->setFiles($files);

        return $this->sendInternalRequest($internalRequest);
    }

    /**
     * {@inheritdoc}
     */
    public function sendRequest(RequestInterface $request)
    {
        if ($request instanceof InternalRequestInterface) {
            return $this->sendInternalRequest($request);
        }

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
     * {@inheritdoc}
     */
    public function sendInternalRequest(InternalRequestInterface $internalRequest)
    {
        $this->eventDispatcher->dispatch(Events::PRE_SEND, new PreSendEvent($internalRequest));

        try {
            $response = $this->doSend($internalRequest);
        } catch (HttpAdapterException $e) {
            $this->eventDispatcher->dispatch(Events::EXCEPTION, new ExceptionEvent($internalRequest, $e));

            throw $e;
        }

        $this->eventDispatcher->dispatch(Events::POST_SEND, new PostSendEvent($internalRequest, $response));

        return $response;
    }

    /**
     * Does a request send.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    abstract protected function doSend(InternalRequestInterface $internalRequest);

    /**
     * Prepares the headers.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     * @param boolean                                             $associative     TRUE if the prepared headers should be associative else FALSE.
     * @param boolean                                             $contentType     TRUE if the content type header should be prepared else FALSE.
     *
     * @return array The prepared headers.
     */
    protected function prepareHeaders(
        InternalRequestInterface $internalRequest,
        $associative = true,
        $contentType = true
    ) {
        if (!$internalRequest->hasHeader('Connection')) {
            $internalRequest->setHeader('Connection', $this->keepAlive ? 'keep-alive' : 'close');
        }

        if (!$internalRequest->hasHeader('Content-Type')) {
            if ($this->hasEncodingType()) {
                $internalRequest->setHeader('Content-Type', $this->encodingType);
            } elseif ($contentType && $internalRequest->hasFiles()) {
                $internalRequest->setHeader('Content-Type', self::ENCODING_TYPE_FORMDATA.'; boundary='.$this->boundary);
            } elseif ($contentType && $internalRequest->hasData()) {
                $internalRequest->setHeader('Content-Type', self::ENCODING_TYPE_URLENCODED);
            }
        }

        return HeadersNormalizer::normalize($internalRequest->getHeaders(), $associative);
    }

    /**
     * Prepares the body.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     *
     * @return string The prepared body.
     */
    protected function prepareBody(InternalRequestInterface $internalRequest)
    {
        if (!$internalRequest->hasFiles()) {
            return $internalRequest->hasArrayData()
                ? http_build_query($internalRequest->getData())
                : $internalRequest->getData();
        }

        $body = '';

        foreach ($internalRequest->getData() as $name => $value) {
            $body .= $this->prepareRawBody($name, $value);
        }

        foreach ($internalRequest->getFiles() as $name => $file) {
            $body .= $this->prepareRawBody($name, $file, true);
        }

        $body .= '--'.$this->boundary.'--'."\r\n";

        return $body;
    }

    /**
     * Prepares the raw body.
     *
     * @param string       $name   The name.
     * @param array|string $data   The data.
     * @param boolean      $isFile TRUE if the data is a file path else FALSE.
     *
     * @return string The formatted raw body.
     */
    protected function prepareRawBody($name, $data, $isFile = false)
    {
        if (is_array($data)) {
            $body = '';

            foreach ($data as $subName => $subData) {
                $body .= $this->prepareRawBody($this->prepareName($name, $subName), $subData, $isFile);
            }

            return $body;
        }

        $body = '--'.$this->boundary."\r\n".'Content-Disposition: form-data; name="'.$name.'"';

        if ($isFile) {
            $body .= '; filename="'.basename($data).'"';
            $data = file_get_contents($data);
        }

        return $body."\r\n\r\n".$data."\r\n";
    }

    /**
     * Prepares the name.
     *
     * @param string $name    The name.
     * @param string $subName The sub name.
     *
     * @return string The prepared name.
     */
    protected function prepareName($name, $subName)
    {
        return $name.'['.$subName.']';
    }

    /**
     * Creates a response.
     *
     * @param string                                            $protocolVersion The protocol version.
     * @param integer                                           $statusCode      The status code.
     * @param string                                            $reasonPhrase    The reason phrase.
     * @param array                                             $headers         The headers.
     * @param resource|string|\Psr\Http\Message\StreamInterface $body            The body.
     * @param string                                            $effectiveUrl    The effective url.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The created response.
     */
    protected function createResponse(
        $protocolVersion,
        $statusCode,
        $reasonPhrase,
        array $headers,
        $body,
        $effectiveUrl
    ) {
        $response = $this->messageFactory->createResponse();
        $response->setProtocolVersion($protocolVersion);
        $response->setStatusCode($statusCode);
        $response->setReasonPhrase($reasonPhrase);
        $response->setHeaders($headers);
        $response->setEffectiveUrl($effectiveUrl);

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
