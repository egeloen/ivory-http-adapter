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
use Ivory\HttpAdapter\Event\Cookie\CookieFactory;
use Ivory\HttpAdapter\Event\Cookie\CookieInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * {@inheritdoc}
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CookieJar implements CookieJarInterface
{
    /** @var \Ivory\HttpAdapter\Event\Cookie\CookieFactoryInterface */
    protected $cookieFactory;

    /** @var array */
    protected $cookies = array();

    /**
     * Creates a cookie jar.
     *
     * @param \Ivory\HttpAdapter\Event\Cookie\CookieFactoryInterface|null $cookieFactory The cookie factory.
     */
    public function __construct(CookieFactoryInterface $cookieFactory = null)
    {
        $this->setCookieFactory($cookieFactory ?: new CookieFactory());
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieFactory()
    {
        return $this->cookieFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function setCookieFactory(CookieFactoryInterface $cookieFactory)
    {
        $this->cookieFactory = $cookieFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function clear($expiredOnly = false)
    {
        if ($expiredOnly) {
            foreach ($this->cookies as $cookie) {
                if ($cookie->isExpired()) {
                    $this->removeCookie($cookie);
                }
            }
        } else {
            $this->cookies = array();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasCookies()
    {
        $this->clear(true);

        return !empty($this->cookies);
    }

    /**
     * {@inheritdoc}
     */
    public function getCookies()
    {
        $this->clear(true);

        return $this->cookies;
    }

    /**
     * {@inheritdoc}
     */
    public function setCookies(array $cookies)
    {
        $this->clear();
        $this->addCookies($cookies);
    }

    /**
     * {@inheritdoc}
     */
    public function addCookies(array $cookies)
    {
        foreach ($cookies as $cookie) {
            $this->addCookie($cookie);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeCookies(array $cookies)
    {
        foreach ($cookies as $cookie) {
            $this->removeCookie($cookie);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasCookie(CookieInterface $cookie)
    {
        return array_search($cookie, $this->getCookies(), true) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function addCookie(CookieInterface $cookie)
    {
        if (!$this->hasCookie($cookie)) {
            $this->cookies[] = $cookie;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeCookie(CookieInterface $cookie)
    {
        unset($this->cookies[array_search($cookie, $this->cookies, true)]);
        $this->cookies = array_values($this->cookies);
    }

    /**
     * {@inheritdoc}
     */
    public function populate(InternalRequestInterface $request)
    {
        foreach ($this->getCookies() as $cookie) {
            if ($cookie->match($request)) {
                $request->addHeader('Cookie', (string) $cookie);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function extract(InternalRequestInterface $request, ResponseInterface $response)
    {
        foreach ($response->getHeaderAsArray('Set-Cookie') as $header) {
            $cookie = $this->cookieFactory->parse($header);

            if (!$cookie->hasAttribute(CookieInterface::ATTR_DOMAIN)) {
                $cookie->setAttribute(
                    CookieInterface::ATTR_DOMAIN,
                    parse_url((string) $request->getUrl(), PHP_URL_HOST)
                );
            }

            $this->addCookie($cookie);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->getCookies());
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getCookies());
    }
}
