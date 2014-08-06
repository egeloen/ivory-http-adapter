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

/**
 * Http adapter exception.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class HttpAdapterException extends \Exception
{
    /**
     * Gets the "CANNOT FETCH URL" exception.
     *
     * @param string $url     The URL.
     * @param string $adapter The adapter name.
     * @param string $error   The error.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException The "CANNOT FETCH URL" exception.
     */
    public static function cannotFetchUrl($url, $adapter, $error)
    {
        return new self(sprintf(
            'An error occurred when fetching the URL "%s" with the adapter "%s" ("%s").',
            $url,
            $adapter,
            $error
        ));
    }

    /**
     * Gets the "DOES NOT SUPPORT DATA AS STRING AND FILES" exception.
     *
     * @param string $adapter The adapter name.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException The "DOES NOT SUPPORT DATA AS STRING AND FILES" exception.
     */
    public static function doesNotSupportDataAsStringAndFiles($adapter)
    {
        return new self(sprintf('The adapter "%s" does not support data as string and files.', $adapter));
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
     * @param string  $url          The url.
     * @param integer $maxRedirects The max redirects.
     * @param string  $adapter      The adapter name.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException The "MAX REDIRECTS EXCEEDED" exception.
     */
    public static function maxRedirectsExceeded($url, $maxRedirects, $adapter)
    {
        return self::cannotFetchUrl($url, $adapter, sprintf('Max redirects exceeded (%d)', $maxRedirects));
    }

    /**
     * Gets the "RESOURCE IS NOT VALID" exception.
     *
     * @param mixed $resource The "RESOURCE IS NOT VALID" exception.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException The "RESOURCE IS NOT VALID" exception.
     */
    public static function resourceIsNotValid($resource)
    {
        return new self(sprintf(
            'The "Ivory\HttpAdapter\Message\ResourceStream" only accepts a valid resource (current: "%s")',
            is_object($resource) ? get_class($resource) : gettype($resource)
        ));
    }

    /**
     * Gets the "TIMEOUT EXCEEDED" exception.
     *
     * @param string $url     The url.
     * @param float  $timeout The timeout.
     * @param string $adapter The adapter name.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException The "TIMEOUT EXCEEDED" exception.
     */
    public static function timeoutExceeded($url, $timeout, $adapter)
    {
        return self::cannotFetchUrl($url, $adapter, sprintf('Timeout exceeded (%.2f)', $timeout));
    }
}
