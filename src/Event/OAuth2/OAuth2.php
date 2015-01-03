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
use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\Message;
use Ivory\HttpAdapter\Message\Request;
use Ivory\HttpAdapter\Message\RequestInterface;
use Ivory\HttpAdapter\Message\Stream\StringStream;

/**
 * OAuth2.
 *
 * @author Jérôme Gamez <jerome@gamez.name>
 */
class OAuth2 implements OAuth2Interface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var HttpAdapterInterface
     */
    private $httpAdapter;

    /**
     * @param HttpAdapterInterface $httpAdapter
     * @param ConfigurationInterface $configuration
     */
    public function __construct(HttpAdapterInterface $httpAdapter, ConfigurationInterface $configuration)
    {
        $this->httpAdapter = $httpAdapter;
        $this->setConfiguration($configuration);
    }

    /**
     * Gets the http adapter.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterInterface The http adapter.
     */
    public function getHttpAdapter()
    {
        return $this->httpAdapter;
    }

    /**
     * Sets the http adapter.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface $httpAdapter The http adapter.
     */
//    public function setHttpAdapter(HttpAdapterInterface $httpAdapter)
//    {
//        $this->httpAdapter = $httpAdapter;
//    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }


    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationUrl(array $options = array())
    {
        $params = array_merge(
            array(
                'client_id' => $this->configuration->getClientId(),
                'client_secret' => $this->configuration->getClientSecret(),
                'redirect_uri' => $this->configuration->getAuthorizationHandlerUrl(),
                'response_type' => 'code',
                'scope' => implode($this->configuration->getScopeSeparator(), $this->configuration->getScopes()),
                'state' => md5(uniqid(rand(), true))
            ),
            $options
        );

        return sprintf('%s?%s', $this->configuration->getAuthorizationUrl(), http_build_query($params));
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessToken(GrantInterface $grant, array $options = array())
    {
        $request = new Request(
            $this->configuration->getAccessTokenUrl(),
            Request::METHOD_POST
        );

        $data = array(
            'client_id' => $this->configuration->getClientId(),
            'client_secret' => $this->configuration->getClientSecret(),
            'grant_type' => (string) $grant,
            'redirect_uri' => $this->configuration->getAuthorizationHandlerUrl(),
        );

        $dataString = http_build_query($data, null, '&');

        $stream = new StringStream($dataString);

        $request->setBody($stream);

        $grant->prepareRequest($request, $options);

        $response = $this->getHttpAdapter()->sendRequest($request);

        return $grant->handleResponse($response);

    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(RequestInterface $request, AccessToken $accessToken)
    {
        $request->setHeader(
            'Authorization',
            sprintf('%s %s', $accessToken->tokenType, $accessToken->accessToken)
        );
    }
}
