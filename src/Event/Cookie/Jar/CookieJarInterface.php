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
 * Cookie jar.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface CookieJarInterface extends \Countable, \IteratorAggregate
{
    /**
     * Gets the cookie factory.
     *
     * @return \Ivory\HttpAdapter\Event\Cookie\CookieFactoryInterface The cookie factory.
     */
    public function getCookieFactory();

    /**
     * Sets the cookie factory.
     *
     * @param \Ivory\HttpAdapter\Event\Cookie\CookieFactoryInterface $cookieFactory The cookie factory.
     */
    public function setCookieFactory(CookieFactoryInterface $cookieFactory);

    /**
     * Cleans the cookies.
     */
    public function clean();

    /**
     * Clears the cookies.
     *
     * @param string|null $domain The domain.
     * @param string|null $path   The path.
     * @param string|null $name   The name.
     */
    public function clear($domain = null, $path = null, $name = null);

    /**
     * Checks if there are cookies.
     *
     * @return boolean TRUE if there are cookies else FALSE.
     */
    public function hasCookies();

    /**
     * Gets the cookies.
     *
     * @return \Ivory\HttpAdapter\Event\Cookie\CookieFactoryInterface[] The cookies.
     */
    public function getCookies();

    /**
     * Sets the cookies.
     *
     * @param \Ivory\HttpAdapter\Event\Cookie\CookieFactoryInterface[] $cookies The cookies.
     */
    public function setCookies(array $cookies);

    /**
     * Adds the cookies.
     *
     * @param \Ivory\HttpAdapter\Event\Cookie\CookieFactoryInterface[] $cookies The cookies.
     */
    public function addCookies(array $cookies);

    /**
     * Removes the cookies.
     *
     * @param \Ivory\HttpAdapter\Event\Cookie\CookieFactoryInterface[] $cookies The cookies.
     */
    public function removeCookies(array $cookies);

    /**
     * Checks if there is the cookie.
     *
     * @param \Ivory\HttpAdapter\Event\Cookie\CookieInterface $cookie The cookie.
     *
     * @return boolean TRUE if there is a cookie else FALSE.
     */
    public function hasCookie(CookieInterface $cookie);

    /**
     * Adds a cookie.
     *
     * @param \Ivory\HttpAdapter\Event\Cookie\CookieInterface $cookie The cookie.
     */
    public function addCookie(CookieInterface $cookie);

    /**
     * Removes a cookie.
     *
     * @param \Ivory\HttpAdapter\Event\Cookie\CookieInterface $cookie The cookie.
     */
    public function removeCookie(CookieInterface $cookie);

    /**
     * Populates the cookies in the request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request The request.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The populated request.
     */
    public function populate(InternalRequestInterface $request);

    /**
     * Extracts the cookies from the request/response.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request  The request.
     * @param \Ivory\HttpAdapter\Message\ResponseInterface        $response The response.
     */
    public function extract(InternalRequestInterface $request, ResponseInterface $response);
}
