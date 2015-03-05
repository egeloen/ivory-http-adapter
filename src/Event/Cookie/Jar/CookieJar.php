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
    private $cookieFactory;

    /** @var \Ivory\HttpAdapter\Event\Cookie\CookieInterface[] */
    private $cookies = array();

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
    public function clean()
    {
        foreach ($this->cookies as $cookie) {
            if ($cookie->isExpired()) {
                $this->removeCookie($cookie);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clear($domain = null, $path = null, $name = null)
    {
        foreach ($this->cookies as $cookie) {
            if ($domain !== null && !$cookie->matchDomain($domain)) {
                continue;
            }

            if ($path !== null && !$cookie->matchPath($path)) {
                continue;
            }

            if ($name !== null && $cookie->getName() !== $name) {
                continue;
            }

            $this->removeCookie($cookie);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasCookies()
    {
        return !empty($this->cookies);
    }

    /**
     * {@inheritdoc}
     */
    public function getCookies()
    {
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
        return array_search($cookie, $this->cookies, true) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function addCookie(CookieInterface $cookie)
    {
        if (!$cookie->hasName() || $this->hasCookie($cookie)) {
            return;
        }

        if (!$cookie->hasValue()) {
            $this->clear(
                $cookie->getAttribute(CookieInterface::ATTR_DOMAIN),
                $cookie->getAttribute(CookieInterface::ATTR_PATH),
                $cookie->getName()
            );

            return;
        }

        foreach ($this->cookies as $jarCookie) {
            if (!$cookie->compare($jarCookie)) {
                continue;
            }

            if ($cookie->getExpires() > $jarCookie->getExpires()) {
                $this->removeCookie($jarCookie);
                continue;
            }

            if ($cookie->getValue() !== $jarCookie->getValue()) {
                $this->removeCookie($jarCookie);
                continue;
            }

            return;
        }

        $this->cookies[] = $cookie;
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
        foreach ($this->cookies as $cookie) {
            if (!$cookie->isExpired() && $cookie->match($request)) {
                $request = $request->withAddedHeader('Cookie', (string) $cookie);
            }
        }

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    public function extract(InternalRequestInterface $request, ResponseInterface $response)
    {
        foreach ($response->getHeader('Set-Cookie') as $header) {
            $cookie = $this->cookieFactory->parse($header);

            if (!$cookie->hasAttribute(CookieInterface::ATTR_DOMAIN)) {
                $cookie->setAttribute(CookieInterface::ATTR_DOMAIN, $request->getUri()->getHost());
            }

            $this->addCookie($cookie);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->cookies);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->cookies);
    }
}
