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
class BasicAuth implements BasicAuthInterface
{
    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var string|callable|null */
    private $matcher;

    /**
     * Creates a basic auth subscriber.
     *
     * @param string               $username The username.
     * @param string               $password The password.
     * @param string|callable|null $matcher  The matcher.
     */
    public function __construct($username, $password, $matcher = null)
    {
        $this->setUsername($username);
        $this->setPassword($password);
        $this->setMatcher($matcher);
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMatcher()
    {
        return $this->matcher !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getMatcher()
    {
        return $this->matcher;
    }

    /**
     * {@inheritdoc}
     */
    public function setMatcher($matcher)
    {
        $this->matcher = $matcher;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(InternalRequestInterface $internalRequest)
    {
        if (!$this->match($internalRequest)) {
            return $internalRequest;
        }

        return $internalRequest->withHeader(
            'Authorization',
            'Basic '.base64_encode($this->username.':'.$this->password)
        );
    }

    /**
     * Checks if the request matches the matcher.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request The request.
     *
     * @return boolean TRUE if the request matches the matcher else FALSE.
     */
    private function match(InternalRequestInterface $request)
    {
        return !$this->hasMatcher()
            || (is_string($this->matcher) && preg_match($this->matcher, (string) $request->getUri()))
            || (is_callable($this->matcher) && call_user_func($this->matcher, $request));
    }
}
