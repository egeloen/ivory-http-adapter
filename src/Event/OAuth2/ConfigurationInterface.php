<?php

/**
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\OAuth2;

/**
 * OAuth2.
 *
 * @author Jérôme Gamez <jerome@gamez.name>
 */
interface ConfigurationInterface
{
    /**
     * @return string
     */
    public function getClientId();

    /**
     * @param string $clientId
     *
     * @return void
     */
    public function setClientId($clientId);

    /**
     * @return string
     */
    public function getClientSecret();

    /**
     * @param string $clientSecret
     *
     * @return void
     */
    public function setClientSecret($clientSecret);

    /**
     * @return array
     */
    public function getScopes();

    /**
     * @param array $scopes
     *
     * @return mixed
     */
    public function setScopes(array $scopes);

    /**
     * @return string
     */
    public function getScopeSeparator();

    /**
     * @param string $scopeSeparator
     *
     * @return void
     */
    public function setScopeSeparator($scopeSeparator);

    /**
     * @return string
     */
    public function getAuthorizationUrl();

    /**
     * @param string $url
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the url is invalid.
     *
     * @return void
     */
    public function setAuthorizationUrl($url);

    /**
     * @return string
     */
    public function getAuthorizationHandlerUrl();

    /**
     * @param string $url
     *
     * @return void
     */
    public function setAuthorizationHandlerUrl($url);

    /**
     * @return string
     */
    public function getAccessTokenUrl();

    /**
     * @param string $url
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the url is invalid.
     *
     * @return void
     */
    public function setAccessTokenUrl($url);
}
