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
use Ivory\HttpAdapter\Normalizer\HeadersNormalizer;
use Psr\Http\Message\OutgoingRequestInterface;

/**
 * Abstract http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractHttpAdapter extends AbstractHttpAdapterTemplate
{
    /** @var \Ivory\HttpAdapter\ConfigurationInterface */
    private $configuration;

    /**
     * Creates an http adapter.
     *
     * @param \Ivory\HttpAdapter\ConfigurationInterface|null $configuration The configuration.
     */
    public function __construct(ConfigurationInterface $configuration = null)
    {
        $this->setConfiguration($configuration ?: new Configuration());
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function send($url, $method, array $headers = array(), $datas = array(), array $files = array())
    {
        return $this->sendInternalRequest($this->configuration->getMessageFactory()->createInternalRequest(
            $url,
            $method,
            $this->configuration->getProtocolVersion(),
            $headers,
            $datas,
            $files
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function sendRequest(OutgoingRequestInterface $request)
    {
        if ($request instanceof InternalRequestInterface) {
            return $this->sendInternalRequest($request);
        }

        $protocolVersion = $this->configuration->getProtocolVersion();
        $this->configuration->setProtocolVersion($request->getProtocolVersion());

        $response = $this->send(
            $request->getUrl(),
            $request->getMethod(),
            $request->getHeaders(),
            (string) $request->getBody()
        );

        $this->configuration->setProtocolVersion($protocolVersion);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function sendMulti(array $requests, $success = null, $error = null)
    {
        foreach ($requests as &$request) {
            if (!$request instanceof OutgoingRequestInterface) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Request must be of type "Psr\Http\Message\OutgoingRequestInterface", "%s" given.',
                        gettype($request) === 'object' ? get_class($request) : gettype($request)
                    )
                );
            }

            if (!$request instanceof InternalRequestInterface) {
                $request = $this->configuration->getMessageFactory()->createInternalRequest(
                    $request->getUrl(),
                    $request->getMethod(),
                    $this->configuration->getProtocolVersion(),
                    $request->getHeaders(),
                    (string) $request->getBody()
                );
            }
        }

        $this->doSendMulti($requests, $success, $error);
    }

    /**
     * Does a sendMulti.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface[] $requests Array or requests.
     * @param callback|null                                         $success  Success callback with instance of \Ivory\HttpAdapter\Message\ResponseInterface and \Psr\Http\Message\OutgoingRequestInterface as arguments.
     * @param callback|null                                         $error    Error callback with instance of \Ivory\HttpAdapter\HttpAdapterException as the argument.
     */
    protected function doSendMulti(array $requests, $success = null, $error = null)
    {
        foreach ($requests as $request) {
            try {
                $response = $this->sendRequest($request);

                if (is_callable($success)) {
                    $success($response, $request);
                }
            } catch (HttpAdapterException $e) {
                if (is_callable($error)) {
                    $error($e);
                }
            }
        }
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
     * @param boolean                                             $contentLength   TRUE if the content length header should be prepared else FALSE.
     *
     * @return array The prepared headers.
     */
    protected function prepareHeaders(
        InternalRequestInterface $internalRequest,
        $associative = true,
        $contentType = true,
        $contentLength = false
    ) {
        if (!$internalRequest->hasHeader('Connection')) {
            $internalRequest->setHeader('Connection', $this->configuration->getKeepAlive() ? 'keep-alive' : 'close');
        }

        if (!$internalRequest->hasHeader('Content-Type')) {
            if ($this->configuration->hasEncodingType()) {
                $internalRequest->setHeader('Content-Type', $this->configuration->getEncodingType());
            } elseif ($contentType && $internalRequest->hasFiles()) {
                $internalRequest->setHeader(
                    'Content-Type',
                    ConfigurationInterface::ENCODING_TYPE_FORMDATA.'; boundary='.$this->configuration->getBoundary()
                );
            } elseif ($contentType && $internalRequest->hasDatas()) {
                $internalRequest->setHeader('Content-Type', ConfigurationInterface::ENCODING_TYPE_URLENCODED);
            }
        }

        if ($contentLength && !$internalRequest->hasHeader('Content-Length')
            && ($length = strlen($this->prepareBody($internalRequest))) > 0) {
            $internalRequest->setHeader('Content-Length', $length);
        }

        if (!$internalRequest->hasHeader('User-Agent')) {
            $internalRequest->setHeader('User-Agent', $this->configuration->getUserAgent());
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
        if ($internalRequest->hasRawDatas()) {
            return $internalRequest->getRawDatas();
        }

        if (!$internalRequest->hasFiles()) {
            return http_build_query($internalRequest->getDatas());
        }

        $body = '';

        foreach ($internalRequest->getDatas() as $name => $value) {
            $body .= $this->prepareRawBody($name, $value);
        }

        foreach ($internalRequest->getFiles() as $name => $file) {
            $body .= $this->prepareRawBody($name, $file, true);
        }

        $body .= '--'.$this->configuration->getBoundary().'--'."\r\n";

        return $body;
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
     * @param string                                                $protocolVersion The protocol version.
     * @param integer                                               $statusCode      The status code.
     * @param string                                                $reasonPhrase    The reason phrase.
     * @param array                                                 $headers         The headers.
     * @param resource|string|\Psr\Http\Message\StreamableInterface $body            The body.
     * @param array                                                 $parameters      The parameters.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The created response.
     */
    protected function createResponse(
        $protocolVersion,
        $statusCode,
        $reasonPhrase,
        array $headers,
        $body,
        array $parameters = array()
    ) {
        return $this->configuration->getMessageFactory()->createResponse(
            $statusCode,
            $reasonPhrase,
            $protocolVersion,
            $headers,
            $body,
            $parameters
        );
    }

    /**
     * {@inheritdoc}
     */
    private function sendInternalRequest(InternalRequestInterface $internalRequest)
    {
        $preSendEvent = new PreSendEvent($this, $internalRequest);

        try {
            $this->configuration->getEventDispatcher()->dispatch(Events::PRE_SEND, $preSendEvent);

            $response = $this->doSend($preSendEvent->getRequest());

            $postSendEvent = new PostSendEvent($this, $preSendEvent->getRequest(), $response);
            $this->configuration->getEventDispatcher()->dispatch(Events::POST_SEND, $postSendEvent);
        } catch (HttpAdapterException $e) {
            $exceptionEvent = new ExceptionEvent($this, $preSendEvent->getRequest(), $e);
            $this->configuration->getEventDispatcher()->dispatch(Events::EXCEPTION, $exceptionEvent);

            if ($exceptionEvent->hasResponse()) {
                return $exceptionEvent->getResponse();
            }

            $exceptionEvent->getException()->setRequest($preSendEvent->getRequest());

            if (isset($response)) {
                $exceptionEvent->getException()->setResponse($response);
            }

            throw $exceptionEvent->getException();
        }

        return $postSendEvent->getResponse();
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
    private function prepareRawBody($name, $data, $isFile = false)
    {
        if (is_array($data)) {
            $body = '';

            foreach ($data as $subName => $subData) {
                $body .= $this->prepareRawBody($this->prepareName($name, $subName), $subData, $isFile);
            }

            return $body;
        }

        $body = '--'.$this->configuration->getBoundary()."\r\n".'Content-Disposition: form-data; name="'.$name.'"';

        if ($isFile) {
            $body .= '; filename="'.basename($data).'"';
            $data = file_get_contents($data);
        }

        return $body."\r\n\r\n".$data."\r\n";
    }
}
