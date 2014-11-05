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
     * Checks if there are headers.
     *
     * @return boolean TRUE if there are headers else FALSE.
     */
    public function hasHeaders();

    /**
     * Checks if there is a body.
     *
     * @return boolean TRUE if there is a body else FALSE.
     */
    public function hasBody();

    /**
     * Clears the parameters.
     *
     * @return void No return value.
     */
    public function clearParameters();

    /**
     * Checks if there are parameters.
     *
     * @return boolean TRUE if there are parameters else FALSE.
     */
    public function hasParameters();

    /**
     * Gets the parameters.
     *
     * @return array The parameters.
     */
    public function getParameters();

    /**
     * Sets the parameters.
     *
     * @param array $parameters The parameters.
     *
     * @return void No return value.
     */
    public function setParameters(array $parameters);

    /**
     * Adds the parameters.
     *
     * @param array $parameters The parameters.
     *
     * @return void No return value.
     */
    public function addParameters(array $parameters);

    /**
     * Removes the parameters.
     *
     * @param array $names The parameter names.
     *
     * @return void No return value.
     */
    public function removeParameters(array $names);

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
     * @return void No return value.
     */
    public function setParameter($name, $value);

    /**
     * Adds a parameter.
     *
     * @param string $name  The parameter name.
     * @param mixed  $value The parameter value.
     *
     * @return void No return value.
     */
    public function addParameter($name, $value);

    /**
     * Removes a parameter.
     *
     * @param string $name The parameter value.
     *
     * @return void No return value.
     */
    public function removeParameter($name);
}
