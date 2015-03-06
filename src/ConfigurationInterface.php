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
 * Configuration interface.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface ConfigurationInterface
{
    /** @const string The url encoded encoding type. */
    const ENCODING_TYPE_URLENCODED = 'application/x-www-form-urlencoded';

    /** @const string The form data encoding type. */
    const ENCODING_TYPE_FORMDATA = 'multipart/form-data';

    /**
     * Gets the message factory.
     *
     * @return \Ivory\HttpAdapter\Message\MessageFactoryInterface The message factory.
     */
    public function getMessageFactory();

    /**
     * Sets the message factory.
     *
     * @param \Ivory\HttpAdapter\Message\MessageFactoryInterface $messageFactory The message factory.
     */
    public function setMessageFactory(MessageFactoryInterface $messageFactory);

    /**
     * Gets the protocol version.
     *
     * @return string The protocol version.
     */
    public function getProtocolVersion();

    /**
     * Sets the protocol version.
     *
     * @param string $protocolVersion The protocol version.
     */
    public function setProtocolVersion($protocolVersion);

    /**
     * Checks if it is kept alive.
     *
     * @return boolean TRUE if it is kept alive.
     */
    public function getKeepAlive();

    /**
     * Sets if it is kept alive.
     *
     * @param boolean $keepAlive TRUE if it is kept alive else FALSE.
     */
    public function setKeepAlive($keepAlive);

    /**
     * Checks if there is an encoding type.
     *
     * @return boolean TRUE if there is an encoding type else FALSE.
     */
    public function hasEncodingType();

    /**
     * Gets the encoding type.
     *
     * @return string|null The encoding type.
     */
    public function getEncodingType();

    /**
     * Sets the encoding type.
     *
     * @param string|null $encodingType The encoding type.
     */
    public function setEncodingType($encodingType);

    /**
     * Gets the boundary.
     *
     * @return string The boundary.
     */
    public function getBoundary();

    /**
     * Sets the boundary.
     *
     * @param string $boundary The boundary.
     */
    public function setBoundary($boundary);

    /**
     * Gets the timeout (in seconds).
     *
     * @return float The timeout.
     */
    public function getTimeout();

    /**
     * Sets the timeout (in seconds).
     *
     * @param float $timeout The timeout.
     */
    public function setTimeout($timeout);

    /**
     * Gets the user agent.
     *
     * @return string The user agent.
     */
    public function getUserAgent();

    /**
     * Sets the user agent.
     *
     * @param string $userAgent The user agent.
     */
    public function setUserAgent($userAgent);

    /**
     * Checks if there is a base uri.
     *
     * @return boolean TRUE if there is a base uri else FALSE.
     */
    public function hasBaseUri();

    /**
     * Gets the base uri.
     *
     * @return string The base uri.
     */
    public function getBaseUri();

    /**
     * Sets the base uri.
     *
     * @param string $baseUri The base uri.
     */
    public function setBaseUri($baseUri);
}
