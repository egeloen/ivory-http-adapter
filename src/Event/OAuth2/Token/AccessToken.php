<?php

/**
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Ivory\HttpAdapter\Event\OAuth2\Token;

/**
 * OAuth2 Access Token.
 *
 * @link http://tools.ietf.org/html/rfc6749#section-5.1 Successful token specification.
 *
 * @author Jérôme Gamez <jerome@gamez.name>
 */
class AccessToken
{
    /**
     * The access token.
     *
     * @var string
     */
    public $accessToken;

    /**
     * The token type.
     *
     * @var string
     */
    public $tokenType;

    /**
     * @var integer
     */
    public $expires;

    /**
     * @var string
     */
    public $refreshToken;

    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->accessToken = $data['access_token'];
        $this->tokenType = $data['token_type'];
        $this->expires = isset($data['expires_in']) ? time() + (int) $data['expires_in'] : null;
        $this->refreshToken = isset($data['refresh_token']) ?: null;
    }
}
