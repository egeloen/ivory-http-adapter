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
 * @author GeLo <geloen.eric@gmail.com>
 */
class SessionCookieJar extends AbstractPersistentCookieJar
{
    /**
     * @var string
     */
    private $key;

    /**
     * @param string                      $key
     * @param CookieFactoryInterface|null $cookieFactory
     */
    public function __construct($key, CookieFactoryInterface $cookieFactory = null)
    {
        $this->setKey($key);

        parent::__construct($cookieFactory);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
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
