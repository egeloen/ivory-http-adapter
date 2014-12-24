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
use Ivory\HttpAdapter\Event\OAuth2\OAuth2Exception;
use Ivory\HttpAdapter\Message\RequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Ivory\HttpAdapter\Message\Stream\StringStream;

/**
 * OAuth2 Grant.
 *
 * @author Jérôme Gamez <jerome@gamez.name>
 */
class RefreshTokenGrant implements GrantInterface
{
    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return 'refresh_token';
    }

    /**
     * {@inheritdoc}
     */
    public function prepareRequest(RequestInterface $request, array $options = array())
    {
        if (!isset($options['refresh_token']) || empty($options['refresh_token'])) {
            throw new \InvalidArgumentException('Missing refresh token');
        }

        parse_str($request->getBody()->getContents(), $data);

        $data += array('refresh_token' => $options['refresh_token']);

        $dataString = http_build_query($data, null, '&');

        $stream = new StringStream($dataString);

        $request->setBody($stream);
    }

    /**
     * {@inheritdoc}
     */
    public function handleResponse(ResponseInterface $response)
    {
        $data = json_decode($response->getBody()->getContents(), true);

        return new AccessToken($data);
    }
}
