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
class AuthorizationCodeGrant implements GrantInterface
{
    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return 'authorization_code';
    }

    /**
     * {@inheritdoc}
     */
    public function prepareRequest(RequestInterface $request, array $options = array())
    {
        if (!isset($options['code']) || empty($options['code'])) {
            throw new \InvalidArgumentException('Missing code');
        }

        parse_str($request->getBody()->getContents(), $data);

        $data += array('code' => $options['code']);

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
