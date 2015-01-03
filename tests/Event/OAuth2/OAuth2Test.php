<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\OAuth2;

use Ivory\HttpAdapter\Event\OAuth2\Token\AccessToken;
use Ivory\HttpAdapter\Event\Oauth2\Configuration;
use Ivory\HttpAdapter\Event\OAuth2\Grant\AuthorizationCodeGrant;
use Ivory\HttpAdapter\Event\OAuth2\Grant\RefreshTokenGrant;
use Ivory\HttpAdapter\Event\OAuth2\OAuth2;
use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\Message\Request;

/**
 * OAuth2 test.
 *
 * @author Jérôme Gamez <jerome@gamez.name>
 */
class OAuth2Test extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\Event\OAuth2\OAuth2 */
    private $oAuth2;

    /** @var \Ivory\HttpAdapter\Event\Oauth2\Configuration */
    private $configuration;

    /** @var HttpAdapterInterface|\PHPUnit_Framework_MockObject_MockObject The http adapter mock. */
    private $httpAdapter;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->httpAdapter = $this->createHttpAdapterMock();
        $this->configuration = $this->getConfiguration();
        $this->oAuth2 = new OAuth2($this->httpAdapter, $this->configuration);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->configuration);
        unset($this->oAuth2);
    }

    /**
     * @return \Ivory\HttpAdapter\Event\Oauth2\Configuration
     */
    protected function getConfiguration()
    {
        $configuration = new Configuration();
        $configuration->setClientId('foo');
        $configuration->setClientSecret('bar');
        $configuration->setAccessTokenUrl('http://example.com/token');
        $configuration->setAuthorizationUrl('http://example.com/auth');
        $configuration->setAuthorizationHandlerUrl('http://egeloen.fr/callback');
        $configuration->setScopes(array('baz', 'qux'));

        return $configuration;
    }

    public function testInitialState()
    {
        $this->assertSame($this->httpAdapter, $this->oAuth2->getHttpAdapter());
        $this->assertSame($this->configuration, $this->oAuth2->getConfiguration());
    }

    public function testSetConfiguration()
    {
        $this->oAuth2->setConfiguration($configuration = new Configuration());

        $this->assertSame($configuration, $this->oAuth2->getConfiguration());
    }

    public function testGetAuthorizationUrl()
    {
        $url = $this->oAuth2->getAuthorizationUrl(array('state' => $state = md5(uniqid(rand(), true))));

        $query = parse_url($url, PHP_URL_QUERY);
        parse_str($query, $queryParts);

        $this->assertStringStartsWith($this->configuration->getAuthorizationUrl(), $url);
        $this->assertEquals($this->configuration->getClientId(), $queryParts['client_id']);
        $this->assertEquals($this->configuration->getClientSecret(), $queryParts['client_secret']);
        $this->assertEquals($this->configuration->getAuthorizationHandlerUrl(), $queryParts['redirect_uri']);
        $this->assertEquals('code', $queryParts['response_type']);
        $this->assertEquals(
            $this->configuration->getScopes(),
            explode($this->configuration->getScopeSeparator(), $queryParts['scope'])
        );
        $this->assertEquals($state, $queryParts['state']);
    }

    public function testGetAccessTokenWithAuthorizationGrant()
    {
        $response = $this->createResponseMockForAuthorizationCodeGrant();

        $this->httpAdapter
            ->expects($this->any())
            ->method('sendRequest')
            ->will($this->returnValue($response));

        $token = $this->oAuth2->getAccessToken(new AuthorizationCodeGrant(), array('code' => 'foo'));

        $this->assertInstanceOf('Ivory\HttpAdapter\Event\OAuth2\Token\AccessToken', $token);
    }

    public function testGetAccessTokenWithAuthorizationGrantAndMissingCode()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Missing code');

        $this->oAuth2->getAccessToken(new AuthorizationCodeGrant());
    }

    public function testGetAccessTokenWithRefreshTokenGrant()
    {
        $response = $this->createResponseMockForRefreshTokenGrant();

        $this->httpAdapter
            ->expects($this->any())
            ->method('sendRequest')
            ->will($this->returnValue($response));

        $token = $this->oAuth2->getAccessToken(new RefreshTokenGrant(), array('refresh_token' => 'foo'));

        $this->assertInstanceOf('Ivory\HttpAdapter\Event\OAuth2\Token\AccessToken', $token);
    }

    public function testGetAccessTokenWithRefreshTokenGrantAndMissingRefreshCode()
    {
        $this->setExpectedException('\InvalidArgumentException','Missing refresh token');

        $this->oAuth2->getAccessToken(new RefreshTokenGrant());
    }

    public function testAuthenticate()
    {
        $token = new AccessToken(array(
            'access_token' => $accessTokenString = 'foo',
            'token_type' => $tokenType = 'bar',
        ));

        $request = new Request('http://egeloen.fr/', Request::METHOD_GET);

        $this->oAuth2->authenticate($request, $token);

        $this->assertArrayHasKey('Authorization', $request->getHeaders());
        $this->assertEquals(sprintf('%s %s', $tokenType, $accessTokenString), $request->getHeader('Authorization'));
    }

    /**
     * Creates a response mock.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject The response mock.
     */
    private function createResponseMockForAuthorizationCodeGrant()
    {
        $body = $this->getMock('Psr\Http\Message\StreamableInterface');
        $body
            ->expects($this->any())
            ->method('getContents')
            ->will($this->returnValue(
                '{"access_token": "foo", "token_type": "Bearer", "expires_in": 3600, "refresh_token": "baz"}'
            ));

        $response = $this->getMock('Ivory\HttpAdapter\Message\ResponseInterface');
        $response
            ->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($body));

        return $response;
    }

    /**
     * Creates a response mock.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject The response mock.
     */
    private function createResponseMockForRefreshTokenGrant()
    {
        $body = $this->getMock('Psr\Http\Message\StreamableInterface');
        $body
            ->expects($this->any())
            ->method('getContents')
            ->will($this->returnValue(
                '{"access_token": "foo", "token_type": "Bearer", "expires_in": 3600}'
            ));

        $response = $this->getMock('Ivory\HttpAdapter\Message\ResponseInterface');
        $response
            ->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($body));

        return $response;
    }

    /**
     * Creates an http adapter mock.
     *
     * @return HttpAdapterInterface|\PHPUnit_Framework_MockObject_MockObject The http adapter mock.
     */
    private function createHttpAdapterMock()
    {
        return $this->getMock('Ivory\HttpAdapter\HttpAdapterInterface');
    }
}
