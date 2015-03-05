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
    const PROTOCOL_VERSION_1_0 = '1.0';

    /** @const string The protocol version 1.1. */
    const PROTOCOL_VERSION_1_1 = '1.1';

    /**
     * Gets the parameters.
     *
     * @return array The parameters.
     */
    public function getParameters();

    /**
     * Checks if there is a parameter.
     *
     * @param string $name The parameter name.
     *
     * @return boolean TRUE if there is the parameter else FALSE.
     */
    public function hasParameter($name);

    /**
     * Gets a parameter.
     *
     * @param string $name The parameter name.
     *
     * @return mixed The parameter value.
     */
    public function getParameter($name);

    /**
     * Sets a parameter.
     *
     * @param string $name  The parameter name.
     * @param mixed  $value The parameter value.
     *
     * @return \Ivory\HttpAdapter\Message\MessageInterface The new message.
     */
    public function withParameter($name, $value);

    /**
     * Adds a parameter.
     *
     * @param string $name  The parameter name.
     * @param mixed  $value The parameter value.
     *
     * @return \Ivory\HttpAdapter\Message\MessageInterface The new message.
     */
    public function withAddedParameter($name, $value);

    /**
     * Removes a parameter.
     *
     * @param string $name The parameter value.
     *
     * @return \Ivory\HttpAdapter\Message\MessageInterface The new message.
     */
    public function withoutParameter($name);
}
