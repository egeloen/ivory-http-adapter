<?php

/**
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Ivory\HttpAdapter\Event\OAuth2\Grant;

use Ivory\HttpAdapter\Event\OAuth2\Token\AccessToken;
use Ivory\HttpAdapter\Message\RequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * OAuth2 Grant.
 *
 * @author Jérôme Gamez <jerome@gamez.name>
 */
interface GrantInterface
{
    /**
     * String representation of this grant.
     *
     * @return string
     */
    public function __toString();

    /**
     * @param RequestInterface $request
     * @param array $options
     *
     * @return void
     */
    public function prepareRequest(RequestInterface $request, array $options = array());

    /**
     * @param ResponseInterface $response
     *
     * @return \Ivory\HttpAdapter\Event\OAuth2\Token\AccessToken
     */
    public function handleResponse(ResponseInterface $response);
}
