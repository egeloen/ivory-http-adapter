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
use Ivory\HttpAdapter\Normalizer\UrlNormalizer;

/**
 * OAuth2 Configuration
 *
 * @author Jérôme Gamez <jerome@gamez.name>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * The client id.
     *
     * @var string
     */
    private $clientId;

    /**
     * The client secret.
     *
     * @var string
     */
    private $clientSecret;

    /**
     * The scopes.
     *
     * @var array
     */
    private $scopes;

    /**
     * The scope separator.
     *
     * The default is a space, as defined in http://tools.ietf.org/html/rfc6749#section-3.3
     *
     * @var string
     */
    private $scopeSeparator = ' ';

    /**
     * The authorization url.
     *
     * @var string
     */
    private $authorizationUrl;

    /**
     * The access token url.
     *
     * @var string
     */
    private $accessTokenUrl;

    /**
     * The authorization handler url.
     *
     * @var string
     */
    private $authorizationHandlerUrl;

    /**
     * The grant.
     *
     * @var GrantInterface
     */
    private $grant;

    /**
     * {@inheritdoc}
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * {@inheritdoc}
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * {@inheritdoc}
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * {@inheritdoc}
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * {@inheritdoc}
     */
    public function setScopes(array $scopes)
    {
        $this->scopes = $scopes;
    }


    /**
     * {@inheritdoc}
     */
    public function getScopeSeparator()
    {
        return $this->scopeSeparator;
    }

    /**
     * {@inheritdoc}
     */
    public function setScopeSeparator($scopeSeparator)
    {
        $this->scopeSeparator = $scopeSeparator;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationUrl()
    {
        return $this->authorizationUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthorizationUrl($url)
    {
        $this->authorizationUrl = UrlNormalizer::normalize($url);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationHandlerUrl()
    {
        return $this->authorizationHandlerUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthorizationHandlerUrl($url)
    {
        $this->authorizationHandlerUrl = UrlNormalizer::normalize($url);
    }


    /**
     * {@inheritdoc}
     */
    public function getAccessTokenUrl()
    {
        return $this->accessTokenUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setAccessTokenUrl($url)
    {
        $this->accessTokenUrl = UrlNormalizer::normalize($url);
    }
}
