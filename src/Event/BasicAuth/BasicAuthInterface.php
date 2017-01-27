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
 * @author GeLo <geloen.eric@gmail.com>
 */
interface BasicAuthInterface
{
    /**
     * @return string
     */
    public function getUsername();

    /**
     * @param string $username
     */
    public function setUsername($username);

    /**
     * @return string
     */
    public function getPassword();

    /**
     * @param string $password
     */
    public function setPassword($password);

    /**
     * @return bool
     */
    public function hasMatcher();

    /**
     * @return string|callable|null
     */
    public function getMatcher();

    /**
     * @param string|callable|null $matcher
     */
    public function setMatcher($matcher);

    /**
     * @param InternalRequestInterface $internalRequest
     *
     * @return InternalRequestInterface
     */
    public function authenticate(InternalRequestInterface $internalRequest);
}
