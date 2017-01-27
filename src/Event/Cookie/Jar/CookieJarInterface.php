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
use Ivory\HttpAdapter\Event\Cookie\CookieInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface CookieJarInterface extends \Countable, \IteratorAggregate
{
    /**
     * @return CookieFactoryInterface
     */
    public function getCookieFactory();

    /**
     * @param CookieFactoryInterface $cookieFactory
     */
    public function setCookieFactory(CookieFactoryInterface $cookieFactory);

    public function clean();

    /**
     * @param string|null $domain
     * @param string|null $path
     * @param string|null $name
     */
    public function clear($domain = null, $path = null, $name = null);

    /**
     * @return bool
     */
    public function hasCookies();

    /**
     * @return CookieFactoryInterface[]
     */
    public function getCookies();

    /**
     * @param CookieFactoryInterface[] $cookies
     */
    public function setCookies(array $cookies);

    /**
     * @param CookieFactoryInterface[] $cookies
     */
    public function addCookies(array $cookies);

    /**
     * @param CookieFactoryInterface[] $cookies
     */
    public function removeCookies(array $cookies);

    /**
     * @param CookieInterface $cookie
     *
     * @return bool
     */
    public function hasCookie(CookieInterface $cookie);

    /**
     * @param CookieInterface $cookie
     */
    public function addCookie(CookieInterface $cookie);

    /**
     * @param CookieInterface $cookie
     */
    public function removeCookie(CookieInterface $cookie);

    /**
     * @param InternalRequestInterface $request
     *
     * @return InternalRequestInterface
     */
    public function populate(InternalRequestInterface $request);

    /**
     * @param InternalRequestInterface $request
     * @param ResponseInterface        $response
     */
    public function extract(InternalRequestInterface $request, ResponseInterface $response);
}
