<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\BasicAuth;

use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * Basic auth.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface BasicAuthInterface
{
    /**
     * Gets the username.
     *
     * @return string The username.
     */
    public function getUsername();

    /**
     * Sets the username.
     *
     * @param string $username The username.
     */
    public function setUsername($username);

    /**
     * Gets the password.
     *
     * @return string The password.
     */
    public function getPassword();

    /**
     * Sets the password.
     *
     * @param string $password The password.
     */
    public function setPassword($password);

    /**
     * Checks if there is a matcher.
     *
     * @return boolean TRUE if there is a matcher else FALSE.
     */
    public function hasMatcher();

    /**
     * Gets the matcher.
     *
     * @return string|callable|null The matcher.
     */
    public function getMatcher();

    /**
     * Sets the matcher.
     *
     * @param string|callable|null $matcher The matcher.
     */
    public function setMatcher($matcher);

    /**
     * Authenticates a request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The authenticated internal request.
     */
    public function authenticate(InternalRequestInterface $internalRequest);
}
