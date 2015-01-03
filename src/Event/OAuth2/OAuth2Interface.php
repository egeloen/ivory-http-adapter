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

use Ivory\HttpAdapter\Event\OAuth2\Grant\GrantInterface;
use Ivory\HttpAdapter\Event\OAuth2\Token\AccessToken;
use Ivory\HttpAdapter\Message\RequestInterface;

/**
 * OAuth2.
 *
 * @author Jérôme Gamez <jerome@gamez.name>
 */
interface OAuth2Interface
{
    /**
     * Gets the configuration.
     *
     * @return \Ivory\HttpAdapter\Event\Oauth2\ConfigurationInterface
     */
    public function getConfiguration();

    /**
     * Sets the configuration.
     *
     * @param ConfigurationInterface $configuration
     *
     * @return mixed
     */
    public function setConfiguration(ConfigurationInterface $configuration);

    /**
     * Returns the authorization url.
     *
     * @link http://tools.ietf.org/html/rfc6749#section-4.1.1 Authorization request specification
     *
     * @param array $options
     *
     * @return mixed
     */
    public function getAuthorizationUrl(array $options = array());

    /**
     * Gets the access token.
     *
     * @link http://tools.ietf.org/html/rfc6749#section-4.1.3 Access token request specification
     *
     * @param GrantInterface $grant The grant.
     * @param array $options The options.
     *
     * @return \Ivory\HttpAdapter\Event\OAuth2\Token\AccessToken
     */
    public function getAccessToken(GrantInterface $grant, array $options = array());

    /**
     * Authenticates a request.
     *
     * @param \Ivory\HttpAdapter\Message\RequestInterface $request The request.
     * @param AccessToken $accessToken The access token.
     *
     * @return void
     */
    public function authenticate(RequestInterface $request, AccessToken $accessToken);
}
