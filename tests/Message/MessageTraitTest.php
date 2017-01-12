<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Message;

use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * Message trait test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MessageTraitTest extends AbstractTestCase
{
    /** @var \Ivory\HttpAdapter\Message\MessageTrait */
    private $message;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->message = $this->getObjectForTrait('Ivory\HttpAdapter\Message\MessageTrait');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->message);
    }

    public function testDefaultState()
    {
        $this->assertEmpty($this->message->getParameters());
    }

    public function testWithParameter()
    {
        $message = $this->message->withParameter($name = 'foo', 'bar');
        $message = $message->withParameter($name = 'foo', $value = 'baz');

        $this->assertNotSame($this->message, $message);
        $this->assertTrue($message->hasParameter($name));
        $this->assertSame($value, $message->getParameter($name));
        $this->assertSame(array($name => $value), $message->getParameters());
    }

    public function testWithAddedParameter()
    {
        $message = $this->message->withAddedParameter($name = 'foo', $value1 = 'bar');
        $message = $message->withAddedParameter($name, $value2 = 'baz');

        $this->assertNotSame($this->message, $message);
        $this->assertTrue($message->hasParameter($name));
        $this->assertSame(array($value1, $value2), $message->getParameter($name));
        $this->assertSame(array($name => array($value1, $value2)), $message->getParameters());
    }

    public function testWithoutParameter()
    {
        $message = $this->message->withParameter($name = 'foo', 'bar');
        $message = $message->withoutParameter($name);

        $this->assertNotSame($this->message, $message);
        $this->assertFalse($message->hasParameter($name));
        $this->assertNull($message->getParameter($name));
        $this->assertEmpty($message->getParameters());
    }
}
