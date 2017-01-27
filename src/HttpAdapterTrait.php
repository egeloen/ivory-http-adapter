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
 * @author GeLo <geloen.eric@gmail.com>
 */
trait HttpAdapterTrait
{
    /**
     * @param string|object $uri
     * @param array         $headers
     *
     * @throws HttpAdapterException
     *
     * @return ResponseInterface
     */
    public function get($uri, array $headers = [])
    {
        return $this->send($uri, InternalRequestInterface::METHOD_GET, $headers);
    }

    /**
     * @param string|object $uri
     * @param array         $headers
     *
     * @throws HttpAdapterException
     *
     * @return ResponseInterface
     */
    public function head($uri, array $headers = [])
    {
        return $this->send($uri, InternalRequestInterface::METHOD_HEAD, $headers);
    }

    /**
     * @param string|object $uri
     * @param array         $headers
     *
     * @throws HttpAdapterException
     *
     * @return ResponseInterface
     */
    public function trace($uri, array $headers = [])
    {
        return $this->send($uri, InternalRequestInterface::METHOD_TRACE, $headers);
    }

    /**
     * @param string|object $uri
     * @param array         $headers
     * @param array|string  $datas
     * @param array         $files
     *
     * @throws HttpAdapterException
     *
     * @return ResponseInterface
     */
    public function post($uri, array $headers = [], $datas = [], array $files = [])
    {
        return $this->send($uri, InternalRequestInterface::METHOD_POST, $headers, $datas, $files);
    }

    /**
     * @param string|object $uri
     * @param array         $headers
     * @param array|string  $datas
     * @param array         $files
     *
     * @throws HttpAdapterException
     *
     * @return ResponseInterface
     */
    public function put($uri, array $headers = [], $datas = [], array $files = [])
    {
        return $this->send($uri, InternalRequestInterface::METHOD_PUT, $headers, $datas, $files);
    }

    /**
     * @param string|object $uri
     * @param array         $headers
     * @param array|string  $datas
     * @param array         $files
     *
     * @throws HttpAdapterException
     *
     * @return ResponseInterface
     */
    public function patch($uri, array $headers = [], $datas = [], array $files = [])
    {
        return $this->send($uri, InternalRequestInterface::METHOD_PATCH, $headers, $datas, $files);
    }

    /**
     * @param string|object $uri
     * @param array         $headers
     * @param array|string  $datas
     * @param array         $files
     *
     * @throws HttpAdapterException
     *
     * @return ResponseInterface
     */
    public function delete($uri, array $headers = [], $datas = [], array $files = [])
    {
        return $this->send($uri, InternalRequestInterface::METHOD_DELETE, $headers, $datas, $files);
    }

    /**
     * @param string|object $uri
     * @param array         $headers
     * @param array|string  $datas
     * @param array         $files
     *
     * @throws HttpAdapterException
     *
     * @return ResponseInterface
     */
    public function options($uri, array $headers = [], $datas = [], array $files = [])
    {
        return $this->send($uri, InternalRequestInterface::METHOD_OPTIONS, $headers, $datas, $files);
    }

    /**
     * @param string|object $uri
     * @param string        $method
     * @param array         $headers
     * @param array|string  $datas
     * @param array         $files
     *
     * @throws HttpAdapterException
     *
     * @return ResponseInterface
     */
    public function send($uri, $method, array $headers = [], $datas = [], array $files = [])
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
     * @param RequestInterface $request
     *
     * @throws HttpAdapterException
     *
     * @return ResponseInterface
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
     * @param array $requests
     *
     * @throws MultiHttpAdapterException
     *
     * @return array
     */
    public function sendRequests(array $requests)
    {
        $responses = $exceptions = [];

        foreach ($requests as $index => &$request) {
            if (is_string($request)) {
                $request = [$request];
            }

            if (is_array($request)) {
                $request = call_user_func_array(
                    [$this->getConfiguration()->getMessageFactory(), 'createInternalRequest'],
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
     * @param InternalRequestInterface $internalRequest
     *
     * @throws HttpAdapterException
     *
     * @return ResponseInterface
     */
    abstract protected function sendInternalRequest(InternalRequestInterface $internalRequest);

    /**
     * @param array    $internalRequests
     * @param callable $success
     * @param callable $error
     *
     * @throws MultiHttpAdapterException
     *
     * @return array
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
