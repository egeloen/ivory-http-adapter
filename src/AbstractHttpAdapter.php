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
use Ivory\HttpAdapter\Event\MultiExceptionEvent;
use Ivory\HttpAdapter\Event\MultiPostSendEvent;
use Ivory\HttpAdapter\Event\MultiPreSendEvent;
use Ivory\HttpAdapter\Event\PostSendEvent;
use Ivory\HttpAdapter\Event\PreSendEvent;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
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
    public function sendRequests(array $requests)
    {
        $exceptions = array();

        foreach ($requests as $index => &$request) {
            if (is_string($request)) {
                $request = array($request);
            }

            if (is_array($request)) {
                $request = call_user_func_array(
                    array($this->configuration->getMessageFactory(), 'createInternalRequest'),
                    $request
                );
            }

            if (!$request instanceof OutgoingRequestInterface) {
                $exceptions[] = HttpAdapterException::requestIsNotValid($request);
                unset($requests[$index]);
            } elseif (!$request instanceof InternalRequestInterface) {
                $request = $this->configuration->getMessageFactory()->createInternalRequest(
                    $request->getUrl(),
                    $request->getMethod(),
                    $request->getProtocolVersion(),
                    $request->getHeaders(),
                    (string) $request->getBody()
                );
            }
        }

        try {
            $responses = $this->sendInternalRequests($requests);
        } catch (MultiHttpAdapterException $e) {
            $exceptions = array_merge($exceptions, $e->getExceptions());
            $responses = $e->getResponses();
        }

        if (!empty($exceptions)) {
            throw new MultiHttpAdapterException($exceptions, $responses);
        }

        return $responses;
    }

    /**
     * Does an internal request send.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    abstract protected function doSendInternalRequest(InternalRequestInterface $internalRequest);

    /**
     * Does internal requests send.
     *
     * @param array    $internalRequests The internal requests.
     * @param callable $success          The success callable.
     * @param callable $error            The error callable.
     */
    protected function doSendInternalRequests(array $internalRequests, $success, $error)
    {
        foreach ($internalRequests as $internalRequest) {
            try {
                $response = $this->doSendInternalRequest($internalRequest);
                $response->setParameter('request', $internalRequest);
                call_user_func($success, $response);
            } catch (HttpAdapterException $e) {
                $e->setRequest($internalRequest);
                $e->setResponse(isset($response) ? $response : null);
                call_user_func($error, $e);
            }
        }
    }

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
            return http_build_query($internalRequest->getDatas(), null, '&');
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
     * Sends an internal request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    private function sendInternalRequest(InternalRequestInterface $internalRequest)
    {
        try {
            if ($this->configuration->hasEventDispatcher()) {
                $this->configuration->getEventDispatcher()->dispatch(
                    Events::PRE_SEND,
                    $preSendEvent = new PreSendEvent($this, $internalRequest)
                );

                $internalRequest = $preSendEvent->getRequest();
            }

            $response = $this->doSendInternalRequest($internalRequest);

            if ($this->configuration->hasEventDispatcher()) {
                $this->configuration->getEventDispatcher()->dispatch(
                    Events::POST_SEND,
                    $postSendEvent = new PostSendEvent($this, $preSendEvent->getRequest(), $response)
                );

                if ($postSendEvent->hasException()) {
                    throw $postSendEvent->getException();
                }

                $response = $postSendEvent->getResponse();
            }
        } catch (HttpAdapterException $e) {
            $e->setRequest($internalRequest);
            $e->setResponse(isset($response) ? $response : null);

            if ($this->configuration->hasEventDispatcher()) {
                $this->configuration->getEventDispatcher()->dispatch(
                    Events::EXCEPTION,
                    $exceptionEvent = new ExceptionEvent($this, $e)
                );

                if ($exceptionEvent->hasResponse()) {
                    return $exceptionEvent->getResponse();
                }

                $e = $exceptionEvent->getException();
            }

            throw $e;
        }

        return $response;
    }

    /**
     * Sends internal requests.
     *
     * @param array $internalRequests The internal requests.
     *
     * @throws \Ivory\HttpAdapter\MultiHttpAdapterException If an error occurred.
     *
     * @return array The responses.
     */
    private function sendInternalRequests(array $internalRequests)
    {
        if (!empty($internalRequests) && $this->configuration->hasEventDispatcher()) {
            $this->configuration->getEventDispatcher()->dispatch(
                Events::MULTI_PRE_SEND,
                $multiPreSendEvent = new MultiPreSendEvent($this, $internalRequests)
            );

            $internalRequests = $multiPreSendEvent->getRequests();
        }

        $responses = array();
        $exceptions = array();

        $successHandler = function (ResponseInterface $response) use (&$responses) {
            $responses[] = $response;
        };

        $errorHandler = function (HttpAdapterException $exception) use (&$exceptions) {
            $exceptions[] = $exception;
        };

        $this->doSendInternalRequests($internalRequests, $successHandler, $errorHandler);

        if (!empty($responses) && $this->configuration->hasEventDispatcher()) {
            $this->configuration->getEventDispatcher()->dispatch(
                Events::MULTI_POST_SEND,
                $postSendEvent = new MultiPostSendEvent($this, $responses)
            );

            $exceptions = array_merge($exceptions, $postSendEvent->getExceptions());
            $responses = $postSendEvent->getResponses();
        }

        if (!empty($exceptions)) {
            if ($this->configuration->hasEventDispatcher()) {
                $this->configuration->getEventDispatcher()->dispatch(
                    Events::MULTI_EXCEPTION,
                    $exceptionEvent = new MultiExceptionEvent($this, $exceptions)
                );

                $responses = array_merge($responses, $exceptionEvent->getResponses());
                $exceptions = $exceptionEvent->getExceptions();
            }

            if (!empty($exceptions)) {
                throw new MultiHttpAdapterException($exceptions, $responses);
            }
        }

        return $responses;
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
