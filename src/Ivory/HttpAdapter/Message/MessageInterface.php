<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Message;

use Psr\Http\Message\MessageInterface as PsrMessageInterface;

/**
 * Message interface.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface MessageInterface extends PsrMessageInterface
{
    /** @const string The protocol version 1.0. */
    const PROTOCOL_VERSION_10 = '1.0';

    /** @const string The protocol version 1.1. */
    const PROTOCOL_VERSION_11 = '1.1';

    /**
     * Sets the protocol version.
     *
     * @param string $protocolVersion The protocol version.
     */
    public function setProtocolVersion($protocolVersion);

    /**
     * Checks if there are headers.
     *
     * @return boolean TRUE if there are headers else FALSE.
     */
    public function hasHeaders();

    /**
     * Removes the headers.
     *
     * @param array $headers The header names.
     */
    public function removeHeaders($headers);

    /**
     * Checks if there is a body.
     *
     * @return boolean TRUE if there is a body else FALSE.
     */
    public function hasBody();
}
