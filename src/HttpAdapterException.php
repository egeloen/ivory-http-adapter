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
 * Http adapter exception.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class HttpAdapterException extends \Exception
{
    /** @var \Ivory\HttpAdapter\Message\InternalRequestInterface|null */
    private $request;

    /** @var \Ivory\HttpAdapter\Message\ResponseInterface|null */
    private $response;

    /**
     * Checks if there is a request.
     *
     * @return boolean TRUE if there is a request else FALSE.
     */
    public function hasRequest()
    {
        return $this->request !== null;
    }

    /**
     * Gets the request.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|null The request.
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Sets the request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface|null $request The request.
     */
    public function setRequest(InternalRequestInterface $request = null)
    {
        $this->request = $request;
    }

    /**
     * Checks if there is a response.
     *
     * @return boolean TRUE if there is a response else FALSE.
     */
    public function hasResponse()
    {
        return $this->response !== null;
    }

    /**
     * Gets the response.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface|null The response.
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets the response.
     *
     * @param \Ivory\HttpAdapter\Message\ResponseInterface|null $response The response.
     */
    public function setResponse(ResponseInterface $response = null)
    {
        $this->response = $response;
    }

    /**
     * Gets the "CANNOT FETCH URI" exception.
     *
     * @param string $uri     The uri.
     * @param string $adapter The adapter name.
     * @param string $error   The error.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException The "CANNOT FETCH URI" exception.
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
     * Gets the "CANNOT LOAD COOKIE JAR" exception.
     *
     * @param string $error The error.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException The "CANNOT LOAD COOKIE JAR" exception.
     */
    public static function cannotLoadCookieJar($error)
    {
        return new self(sprintf('An error occurred when loading the cookie jar ("%s").', $error));
    }

    /**
     * Gets the "CANNOT SAVE COOKIE JAR" exception.
     *
     * @param string $error The error.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException The "CANNOT SAVE COOKIE JAR" exception.
     */
    public static function cannotSaveCookieJar($error)
    {
        return new self(sprintf('An error occurred when saving the cookie jar ("%s").', $error));
    }

    /**
     * Gets the "HTTP ADAPTER DOES NOT EXIST" exception.
     *
     * @param string $name The name.
     *
     * @return HttpAdapterException The "HTTP ADAPTER DOES NOT EXIST" exception.
     */
    public static function httpAdapterDoesNotExist($name)
    {
        return new self(sprintf('The http adapter "%s" does not exist.', $name));
    }

    /**
     * Gets the "HTTP ADAPTER IS NOT USABLE" exception.
     *
     * @param string $name The name.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException The "HTTP ADAPTER IS NOT USABLE" exception.
     */
    public static function httpAdapterIsNotUsable($name)
    {
        return new self(sprintf('The http adapter "%s" is not usable.', $name));
    }

    /**
     * Gets the "HTTP ADAPTERS ARE NOT USABLE" exception.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException The "HTTP ADAPTERS ARE NOT USABLE" exception.
     */
    public static function httpAdaptersAreNotUsable()
    {
        return new self('No http adapters are usable.');
    }

    /**
     * Gets the "HTTP ADAPTER MUST IMPLEMENT INTERFACE" exception.
     *
     * @param string $class The class.
     *
     * @return HttpAdapterException The "HTTP ADAPTER MUST IMPLEMENT INTERFACE" exception.
     */
    public static function httpAdapterMustImplementInterface($class)
    {
        return new self(sprintf('The class "%s" must implement "Ivory\HttpAdapter\HttpAdapterInterface".', $class));
    }

    /**
     * Gets the "DOES NOT SUPPORT SUB ADAPTER" exception.
     *
     * @param string $adapter    The adapter name.
     * @param string $subAdapter The sub adapter name.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException The "DOES NOT SUPPORT SUB ADAPTER" exception.
     */
    public static function doesNotSupportSubAdapter($adapter, $subAdapter)
    {
        return new self(sprintf('The adapter "%s" does not support the sub-adapter "%s".', $adapter, $subAdapter));
    }

    /**
     * Gets the "EXTENSION IS NOT LOADED" exception.
     *
     * @param string $extension The extension name.
     * @param string $adapter   The adapter name.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException The "EXTENSION IS NOT LOADED" exception.
     */
    public static function extensionIsNotLoaded($extension, $adapter)
    {
        return new self(sprintf('The adapter "%s" expects the PHP extension "%s" to be loaded.', $adapter, $extension));
    }

    /**
     * Gets the "MAX REDIRECTS EXCEEDED" exception.
     *
     * @param string  $uri          The uri.
     * @param integer $maxRedirects The max redirects.
     * @param string  $adapter      The adapter name.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException The "MAX REDIRECTS EXCEEDED" exception.
     */
    public static function maxRedirectsExceeded($uri, $maxRedirects, $adapter)
    {
        return self::cannotFetchUri($uri, $adapter, sprintf('Max redirects exceeded (%d)', $maxRedirects));
    }

    /**
     * Gets the "REQUEST IS NOT VALID" exception.
     *
     * @param mixed $request The request.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException The "REQUEST IS NOT VALID" exception.
     */
    public static function requestIsNotValid($request)
    {
        return new self(sprintf(
            'The request must be a string, an array or implement "Psr\Http\Message\RequestInterface" ("%s" given).',
            is_object($request) ? get_class($request) : gettype($request)
        ));
    }

    /**
     * Gets the "STREAM IS NOT VALID" exception.
     *
     * @param mixed  $stream   The stream.
     * @param string $wrapper  The wrapper.
     * @param string $expected The expected.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException The "STREAM IS NOT VALID" exception.
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
     * Gets the "TIMEOUT EXCEEDED" exception.
     *
     * @param string $uri     The uri.
     * @param float  $timeout The timeout.
     * @param string $adapter The adapter name.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException The "TIMEOUT EXCEEDED" exception.
     */
    public static function timeoutExceeded($uri, $timeout, $adapter)
    {
        return self::cannotFetchUri($uri, $adapter, sprintf('Timeout exceeded (%.2f)', $timeout));
    }
}
