<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\Subscriber;

use Ivory\HttpAdapter\Event\Events;
use Ivory\HttpAdapter\Event\Subscriber\BasicAuthSubscriber;
use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * Basic auth subscriber test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class BasicAuthSubscriberTest extends AbstractSubscriberTest
{
    /** @var \Ivory\HttpAdapter\Event\Subscriber\BasicAuthSubscriber */
    protected $basicAuthSubscriber;

    /** @var string */
    protected $username;

    /** @var string */
    protected $password;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->basicAuthSubscriber = new BasicAuthSubscriber(
            $this->username = 'username',
            $this->password = 'password'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->basicAuthSubscriber);
        unset($this->username);
        unset($this->password);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->username, $this->basicAuthSubscriber->getUsername());
        $this->assertSame($this->password, $this->basicAuthSubscriber->getPassword());
        $this->assertFalse($this->basicAuthSubscriber->hasMatcher());
    }

    public function testInitialState()
    {
        $this->basicAuthSubscriber = new BasicAuthSubscriber(
            $this->username = 'username',
            $this->password = 'password',
            $matcher = '/^foo$/'
        );

        $this->assertTrue($this->basicAuthSubscriber->hasMatcher());
        $this->assertSame($matcher, $this->basicAuthSubscriber->getMatcher());
    }

    public function testSetUsername()
    {
        $this->basicAuthSubscriber->setUsername($username = 'foo');

        $this->assertSame($username, $this->basicAuthSubscriber->getUsername());
    }

    public function testSetPassword()
    {
        $this->basicAuthSubscriber->setPassword($password = 'foo');

        $this->assertSame($password, $this->basicAuthSubscriber->getPassword());
    }

    /**
     * @dataProvider validMatcherProvider
     */
    public function testSetMatcher($matcher)
    {
        $this->basicAuthSubscriber->setMatcher($matcher);

        $this->assertSame($matcher, $this->basicAuthSubscriber->getMatcher());
    }

    public function testSubscribedEvents()
    {
        $events = BasicAuthSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::PRE_SEND, $events);
        $this->assertSame('onPreSend', $events[Events::PRE_SEND]);
    }

    /**
     * @dataProvider validMatcherProvider
     */
    public function testPreSendEventWithValidMatcher($matcher)
    {
        $this->basicAuthSubscriber->setMatcher($matcher);
        $this->basicAuthSubscriber->onPreSend($this->createPreSendEvent($request = $this->createRequest()));

        $this->assertTrue($request->hasHeader('Authorization'));
        $this->assertSame('Basic dXNlcm5hbWU6cGFzc3dvcmQ=', $request->getHeader('authorization'));
    }

    /**
     * @dataProvider invalidMatcherProvider
     */
    public function testPreSendEventWithInvalidMatcher($matcher)
    {
        $this->basicAuthSubscriber->setMatcher($matcher);
        $this->basicAuthSubscriber->onPreSend($this->createPreSendEvent($request = $this->createRequest()));

        $this->assertFalse($request->hasHeader('Authorization'));
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
            array(function (InternalRequestInterface $request) {
                return $request->getUrl() === 'http://egeloen.fr';
            }),
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
            array(function (InternalRequestInterface $request) {
                return $request->getUrl() === 'foo';
            }),
        );
    }
}
