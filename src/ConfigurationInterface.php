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
 * @author GeLo <geloen.eric@gmail.com>
 */
interface ConfigurationInterface
{
    const ENCODING_TYPE_URLENCODED = 'application/x-www-form-urlencoded';
    const ENCODING_TYPE_FORMDATA = 'multipart/form-data';

    /**
     * @return MessageFactoryInterface
     */
    public function getMessageFactory();

    /**
     * @param MessageFactoryInterface $messageFactory
     */
    public function setMessageFactory(MessageFactoryInterface $messageFactory);

    /**
     * @return string
     */
    public function getProtocolVersion();

    /**
     * @param string $protocolVersion
     */
    public function setProtocolVersion($protocolVersion);

    /**
     * @return bool
     */
    public function getKeepAlive();

    /**
     * @param bool $keepAlive
     */
    public function setKeepAlive($keepAlive);

    /**
     * @return bool
     */
    public function hasEncodingType();

    /**
     * @return string|null
     */
    public function getEncodingType();

    /**
     * @param string|null $encodingType
     */
    public function setEncodingType($encodingType);

    /**
     * @return string
     */
    public function getBoundary();

    /**
     * @param string $boundary
     */
    public function setBoundary($boundary);

    /**
     * @return float
     */
    public function getTimeout();

    /**
     * @param float $timeout
     */
    public function setTimeout($timeout);

    /**
     * @return string
     */
    public function getUserAgent();

    /**
     * @param string $userAgent
     */
    public function setUserAgent($userAgent);

    /**
     * @return bool
     */
    public function hasBaseUri();

    /**
     * @return string
     */
    public function getBaseUri();

    /**
     * @param string $baseUri
     */
    public function setBaseUri($baseUri);
}
