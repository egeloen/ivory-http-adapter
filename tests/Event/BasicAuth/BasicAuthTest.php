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
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BasicAuthTest extends AbstractTestCase
{
    /**
     * @var BasicAuth
     */
    private $basicAuth;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->basicAuth = new BasicAuth($this->username = 'username', $this->password = 'password');
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
            ->method('withHeader')
            ->with(
                $this->identicalTo('Authorization'),
                $this->identicalTo('Basic dXNlcm5hbWU6cGFzc3dvcmQ=')
            )
            ->will($this->returnValue($authenticatedRequest = $this->createRequestMock()));

        $this->assertSame($authenticatedRequest, $this->basicAuth->authenticate($request));
    }

    /**
     * @param mixed $matcher
     *
     * @dataProvider validMatcherProvider
     */
    public function testAuthenticateWithValidMatcher($matcher)
    {
        $this->basicAuth->setMatcher($matcher);

        $request = $this->createRequestMock();
        $request
            ->expects($this->once())
            ->method('withHeader')
            ->with(
                $this->identicalTo('Authorization'),
                $this->identicalTo('Basic dXNlcm5hbWU6cGFzc3dvcmQ=')
            )
            ->will($this->returnValue($authenticatedRequest = $this->createRequestMock()));

        $this->assertSame($authenticatedRequest, $this->basicAuth->authenticate($request));
    }

    /**
     * @param mixed $matcher
     *
     * @dataProvider invalidMatcherProvider
     */
    public function testAuthenticateWithInvalidMatcher($matcher)
    {
        $this->basicAuth->setMatcher($matcher);

        $request = $this->createRequestMock();
        $request
            ->expects($this->never())
            ->method('withHeader');

        $this->assertSame($request, $this->basicAuth->authenticate($request));
    }

    /**
     * @return array
     */
    public function validMatcherProvider()
    {
        return [
            [null],
            ['/^http:\/\/egeloen\.fr$/'],
            [
                function (InternalRequestInterface $request) {
                    return $request->getUri() === 'http://egeloen.fr';
                },
            ],
        ];
    }

    /**
     * @return array
     */
    public function invalidMatcherProvider()
    {
        return [
            ['/^foo$/'],
            [
                function (InternalRequestInterface $request) {
                    return $request->getUri() === 'foo';
                },
            ],
        ];
    }

    /**
     * @return InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject the request mock
     */
    private function createRequestMock()
    {
        $request = $this->createMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
        $request
            ->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue('http://egeloen.fr'));

        return $request;
    }
}
