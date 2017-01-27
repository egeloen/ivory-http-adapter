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
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class HttpAdapterException extends \Exception
{
    /**
     * @var InternalRequestInterface|null
     */
    private $request;

    /**
     * @var ResponseInterface|null
     */
    private $response;

    /**
     * @return bool
     */
    public function hasRequest()
    {
        return $this->request !== null;
    }

    /**
     * @return InternalRequestInterface|null
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param InternalRequestInterface|null $request
     */
    public function setRequest(InternalRequestInterface $request = null)
    {
        $this->request = $request;
    }

    /**
     * @return bool
     */
    public function hasResponse()
    {
        return $this->response !== null;
    }

    /**
     * @return ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface|null $response
     */
    public function setResponse(ResponseInterface $response = null)
    {
        $this->response = $response;
    }

    /**
     * @param string $uri
     * @param string $adapter
     * @param string $error
     *
     * @return HttpAdapterException
     */
    public static function cannotFetchUri($uri, $adapter, $error)
    {
        return new self(sprintf(
            'An error occurred when fetching the URI "%s" with the adapter "%s" ("%s").',
            $uri,
            $adapter,
            $error
        ));
    }

    /**
     * @param string $error
     *
     * @return HttpAdapterException
     */
    public static function cannotLoadCookieJar($error)
    {
        return new self(sprintf('An error occurred when loading the cookie jar ("%s").', $error));
    }

    /**
     * @param string $error
     *
     * @return HttpAdapterException
     */
    public static function cannotSaveCookieJar($error)
    {
        return new self(sprintf('An error occurred when saving the cookie jar ("%s").', $error));
    }

    /**
     * @param string $name
     *
     * @return HttpAdapterException
     */
    public static function httpAdapterDoesNotExist($name)
    {
        return new self(sprintf('The http adapter "%s" does not exist.', $name));
    }

    /**
     * @param string $name
     *
     * @return HttpAdapterException
     */
    public static function httpAdapterIsNotUsable($name)
    {
        return new self(sprintf('The http adapter "%s" is not usable.', $name));
    }

    /**
     * @return HttpAdapterException
     */
    public static function httpAdaptersAreNotUsable()
    {
        return new self('No http adapters are usable.');
    }

    /**
     * @param string $class
     *
     * @return HttpAdapterException
     */
    public static function httpAdapterMustImplementInterface($class)
    {
        return new self(sprintf('The class "%s" must implement "Ivory\HttpAdapter\HttpAdapterInterface".', $class));
    }

    /**
     * @param string $adapter
     * @param string $subAdapter
     *
     * @return HttpAdapterException
     */
    public static function doesNotSupportSubAdapter($adapter, $subAdapter)
    {
        return new self(sprintf('The adapter "%s" does not support the sub-adapter "%s".', $adapter, $subAdapter));
    }

    /**
     * @param string $extension
     * @param string $adapter
     *
     * @return HttpAdapterException
     */
    public static function extensionIsNotLoaded($extension, $adapter)
    {
        return new self(sprintf('The adapter "%s" expects the PHP extension "%s" to be loaded.', $adapter, $extension));
    }

    /**
     * @param string $uri
     * @param int    $maxRedirects
     * @param string $adapter
     *
     * @return HttpAdapterException
     */
    public static function maxRedirectsExceeded($uri, $maxRedirects, $adapter)
    {
        return self::cannotFetchUri($uri, $adapter, sprintf('Max redirects exceeded (%d)', $maxRedirects));
    }

    /**
     * @param mixed $request
     *
     * @return HttpAdapterException
     */
    public static function requestIsNotValid($request)
    {
        return new self(sprintf(
            'The request must be a string, an array or implement "Psr\Http\Message\RequestInterface" ("%s" given).',
            is_object($request) ? get_class($request) : gettype($request)
        ));
    }

    /**
     * @param mixed  $stream
     * @param string $wrapper
     * @param string $expected
     *
     * @return HttpAdapterException
     */
    public static function streamIsNotValid($stream, $wrapper, $expected)
    {
        return new self(sprintf(
            'The stream "%s" only accepts a "%s" (current: "%s")',
            $wrapper,
            $expected,
            is_object($stream) ? get_class($stream) : gettype($stream)
        ));
    }

    /**
     * @param string $uri
     * @param float  $timeout
     * @param string $adapter
     *
     * @return HttpAdapterException
     */
    public static function timeoutExceeded($uri, $timeout, $adapter)
    {
        return self::cannotFetchUri($uri, $adapter, sprintf('Timeout exceeded (%.2f)', $timeout));
    }
}
