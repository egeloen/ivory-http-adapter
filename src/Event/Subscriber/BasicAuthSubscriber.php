<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\Subscriber;

use Ivory\HttpAdapter\Event\Events;
use Ivory\HttpAdapter\Event\PreSendEvent;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Basic auth subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class BasicAuthSubscriber implements EventSubscriberInterface
{
    /** @var string */
    protected $username;

    /** @var string */
    protected $password;

    /** @var string|callable|null */
    protected $matcher;

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
     * Gets the username.
     *
     * @return string The username.
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets the username.
     *
     * @param string $username The username.
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Gets the password.
     *
     * @return string The password.
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets the password.
     *
     * @param string $password The password.
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Checks if there is a matcher.
     *
     * @return boolean TRUE if there is a matcher else FALSE.
     */
    public function hasMatcher()
    {
        return $this->matcher !== null;
    }

    /**
     * Gets the matcher.
     *
     * @return string|callable|null The matcher.
     */
    public function getMatcher()
    {
        return $this->matcher;
    }

    /**
     * Sets the matcher.
     *
     * @param string|callable|null $matcher The matcher.
     */
    public function setMatcher($matcher)
    {
        $this->matcher = $matcher;
    }

    /**
     * On pre send event.
     *
     * @param \Ivory\HttpAdapter\Event\PreSendEvent $event The pre send event.
     */
    public function onPreSend(PreSendEvent $event)
    {
        if ($this->match($event->getRequest())) {
            $event->getRequest()->addHeader(
                'Authorization',
                'Basic '.base64_encode($this->username.':'.$this->password)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(Events::PRE_SEND => array('onPreSend', 300));
    }

    /**
     * Checks if the request matches the matcher.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request The request.
     *
     * @return boolean TRUE if the request matches the matcher else FALSE.
     */
    protected function match(InternalRequestInterface $request)
    {
        if (!$this->hasMatcher()) {
            return true;
        }

        if (is_string($this->matcher) && preg_match($this->matcher, (string) $request->getUrl())) {
            return true;
        }

        if (is_callable($this->matcher)) {
            return call_user_func($this->matcher, $request);
        }

        return false;
    }
}
