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

use Ivory\HttpAdapter\Event\OAuth2\Configuration;

/**
 * Configuration test.
 *
 * @author Jérôme Gamez <jerome@gamez.name>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testInitializeConfiguration()
    {
        $configuration = new Configuration();

        $configuration->setClientId($clientId = 'foo');
        $configuration->setClientSecret($clientSecret = 'bar');
        $configuration->setAccessTokenUrl($accessTokenUrl = 'http://example.com/token');
        $configuration->setAuthorizationUrl($authorizationUrl = 'http://example.com/auth');
        $configuration->setAuthorizationHandlerUrl($authorizationHandlerUrl = 'http://egeloen.fr/callback');
        $configuration->setScopes($scopes = array('baz', 'meh'));
        $configuration->setScopeSeparator($scopeSeparator = '/');

        $this->assertSame($clientId, $configuration->getClientId());
        $this->assertSame($clientSecret, $configuration->getClientSecret());
        $this->assertSame($accessTokenUrl, $configuration->getAccessTokenUrl());
        $this->assertSame($authorizationUrl, $configuration->getAuthorizationUrl());
        $this->assertSame($authorizationHandlerUrl, $configuration->getAuthorizationHandlerUrl());
        $this->assertSame($scopes, $configuration->getScopes());
        $this->assertSame($scopeSeparator, $configuration->getScopeSeparator());
    }
}
