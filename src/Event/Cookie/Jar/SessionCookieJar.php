<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\Cookie\Jar;

use Ivory\HttpAdapter\Event\Cookie\CookieFactoryInterface;

/**
 * Session cookie jar.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class SessionCookieJar extends AbstractPersistentCookieJar
{
    /** @var string */
    private $key;

    /**
     * Creates a session cookie jar.
     *
     * @param string                                                      $key           The key.
     * @param \Ivory\HttpAdapter\Event\Cookie\CookieFactoryInterface|null $cookieFactory The cookie factory.
     */
    public function __construct($key, CookieFactoryInterface $cookieFactory = null)
    {
        $this->setKey($key);

        parent::__construct($cookieFactory);
    }

    /**
     * Gets the key.
     *
     * @return string The key.
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Sets the key.
     *
     * @param string $key The key.
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        $this->unserialize(isset($_SESSION[$this->key]) ? $_SESSION[$this->key] : null);
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $_SESSION[$this->key] = $this->serialize();
    }
}
