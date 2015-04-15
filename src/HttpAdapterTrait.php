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
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Http adapter trait.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
trait HttpAdapterTrait
{
    /**
     * {@inheritdoc}
     */
    public function get($uri, array $headers = array())
    {
        return $this->send($uri, InternalRequestInterface::METHOD_GET, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function head($uri, array $headers = array())
    {
        return $this->send($uri, InternalRequestInterface::METHOD_HEAD, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function trace($uri, array $headers = array())
    {
        return $this->send($uri, InternalRequestInterface::METHOD_TRACE, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function post($uri, array $headers = array(), $datas = array(), array $files = array())
    {
        return $this->send($uri, InternalRequestInterface::METHOD_POST, $headers, $datas, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function put($uri, array $headers = array(), $datas = array(), array $files = array())
    {
        return $this->send($uri, InternalRequestInterface::METHOD_PUT, $headers, $datas, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function patch($uri, array $headers = array(), $datas = array(), array $files = array())
    {
        return $this->send($uri, InternalRequestInterface::METHOD_PATCH, $headers, $datas, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($uri, array $headers = array(), $datas = array(), array $files = array())
    {
        return $this->send($uri, InternalRequestInterface::METHOD_DELETE, $headers, $datas, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function options($uri, array $headers = array(), $datas = array(), array $files = array())
    {
        return $this->send($uri, InternalRequestInterface::METHOD_OPTIONS, $headers, $datas, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function send($uri, $method, array $headers = array(), $datas = array(), array $files = array())
    {
        return $this->sendRequest($this->getConfiguration()->getMessageFactory()->createInternalRequest(
            $uri,
            $method,
            $this->getConfiguration()->getProtocolVersion(),
            $headers,
            $datas,
            $files
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function sendRequest(RequestInterface $request)
    {
        if ($request instanceof InternalRequestInterface) {
            return $this->sendInternalRequest($request);
        }

        $protocolVersion = $this->getConfiguration()->getProtocolVersion();
        $this->getConfiguration()->setProtocolVersion($request->getProtocolVersion());

        $response = $this->send(
            $request->getUri(),
            $request->getMethod(),
            $request->getHeaders(),
            $request->getBody()
        );

        $this->getConfiguration()->setProtocolVersion($protocolVersion);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function sendRequests(array $requests)
    {
        $responses = $exceptions = array();

        foreach ($requests as $index => &$request) {
            if (is_string($request)) {
                $request = array($request);
            }

            if (is_array($request)) {
                $request = call_user_func_array(
                    array($this->getConfiguration()->getMessageFactory(), 'createInternalRequest'),
                    $request
                );
            }

            if (!$request instanceof RequestInterface) {
                $exceptions[] = HttpAdapterException::requestIsNotValid($request);
                unset($requests[$index]);
            } elseif (!$request instanceof InternalRequestInterface) {
                $request = $this->getConfiguration()->getMessageFactory()->createInternalRequest(
                    $request->getUri(),
                    $request->getMethod(),
                    $request->getProtocolVersion(),
                    $request->getHeaders(),
                    $request->getBody()
                );
            }
        }

        $success = function (ResponseInterface $response) use (&$responses) {
            $responses[] = $response;
        };

        $error = function (HttpAdapterException $exception) use (&$exceptions) {
            $exceptions[] = $exception;
        };

        $this->sendInternalRequests($requests, $success, $error);

        if (!empty($exceptions)) {
            throw new MultiHttpAdapterException($exceptions, $responses);
        }

        return $responses;
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
    abstract protected function sendInternalRequest(InternalRequestInterface $internalRequest);

    /**
     * Sends internal requests.
     *
     * @param array    $internalRequests The internal requests.
     * @param callable $success          The success callable.
     * @param callable $error            The error callable.
     *
     * @throws \Ivory\HttpAdapter\MultiHttpAdapterException If an error occurred.
     *
     * @return array The responses.
     */
    protected function sendInternalRequests(array $internalRequests, $success, $error)
    {
        foreach ($internalRequests as $internalRequest) {
            try {
                $response = $this->sendInternalRequest($internalRequest);
                $response = $response->withParameter('request', $internalRequest);
                call_user_func($success, $response);
            } catch (HttpAdapterException $e) {
                $e->setRequest($internalRequest);
                $e->setResponse(isset($response) ? $response : null);
                call_user_func($error, $e);
            }
        }
    }
}
