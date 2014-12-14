<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\BasicAuth;

use Ivory\HttpAdapter\Event\BasicAuth\BasicAuth;
use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * Basic auth test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class BasicAuthTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\Event\BasicAuth\BasicAuth */
    private $basicAuth;

    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->basicAuth = new BasicAuth($this->username = 'username', $this->password = 'password');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->password);
        unset($this->username);
        unset($this->basicAuth);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->username, $this->basicAuth->getUsername());
        $this->assertSame($this->password, $this->basicAuth->getPassword());
        $this->assertFalse($this->basicAuth->hasMatcher());
    }

    public function testInitialState()
    {
        $this->basicAuth = new BasicAuth($this->username, $this->password, $matcher = '/^foo$/');

        $this->assertTrue($this->basicAuth->hasMatcher());
        $this->assertSame($matcher, $this->basicAuth->getMatcher());
    }

    public function testSetUsername()
    {
        $this->basicAuth->setUsername($username = 'foo');

        $this->assertSame($username, $this->basicAuth->getUsername());
    }

    public function testSetPassword()
    {
        $this->basicAuth->setPassword($password = 'foo');

        $this->assertSame($password, $this->basicAuth->getPassword());
    }

    /**
     * @dataProvider validMatcherProvider
     */
    public function testSetMatcher($matcher)
    {
        $this->basicAuth->setMatcher($matcher);

        $this->assertSame($matcher, $this->basicAuth->getMatcher());
    }

    public function testAuthenticateWithoutMatcher()
    {
        $request = $this->createRequestMock();
        $request
            ->expects($this->once())
            ->method('addHeader')
            ->with(
                $this->identicalTo('Authorization'),
                $this->identicalTo('Basic dXNlcm5hbWU6cGFzc3dvcmQ=')
            );

        $this->basicAuth->authenticate($request);
    }

    /**
     * @dataProvider validMatcherProvider
     */
    public function testAuthenticateWithValidMatcher($matcher)
    {
        $this->basicAuth->setMatcher($matcher);

        $request = $this->createRequestMock();
        $request
            ->expects($this->once())
            ->method('addHeader')
            ->with(
                $this->identicalTo('Authorization'),
                $this->identicalTo('Basic dXNlcm5hbWU6cGFzc3dvcmQ=')
            );

        $this->basicAuth->authenticate($request);
    }

    /**
     * @dataProvider invalidMatcherProvider
     */
    public function testAuthenticateWithInvalidMatcher($matcher)
    {
        $this->basicAuth->setMatcher($matcher);

        $request = $this->createRequestMock();
        $request
            ->expects($this->never())
            ->method('addHeader');

        $this->basicAuth->authenticate($request);
    }

    /**
     * Gets the valid matcher provider.
     *
     * @return array The valid matcher provider.
     */
    public function validMatcherProvider()
    {
        return array(
            array(null),
            array('/^http:\/\/egeloen\.fr$/'),
            array(
                function (InternalRequestInterface $request) {
                    return $request->getUrl() === 'http://egeloen.fr';
                },
            ),
        );
    }

    /**
     * Gets the invalid matcher provider.
     *
     * @return array The invalid matcher provider.
     */
    public function invalidMatcherProvider()
    {
        return array(
            array('/^foo$/'),
            array(
                function (InternalRequestInterface $request) {
                    return $request->getUrl() === 'foo';
                },
            ),
        );
    }

    /**
     * Creates a request mock.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject The request mock.
     */
    private function createRequestMock()
    {
        $request = $this->getMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
        $request
            ->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue('http://egeloen.fr'));

        return $request;
    }
}
