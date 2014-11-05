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

use Ivory\HttpAdapter\Message\MessageInterface;

/**
 * Message test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\Message\AbstractMessage|\PHPUnit_Framework_MockObject_MockObject */
    protected $message;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->message = $this->getMockForAbstractClass('Ivory\HttpAdapter\Message\AbstractMessage');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->message);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf('Psr\Http\Message\MessageInterface', $this->message);
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\MessageInterface', $this->message);
    }

    public function testDefaultState()
    {
        $this->assertSame(MessageInterface::PROTOCOL_VERSION_1_1, $this->message->getProtocolVersion());

        $this->assertFalse($this->message->hasHeaders());
        $this->assertEmpty($this->message->getHeaders());

        $this->assertFalse($this->message->hasBody());
        $this->assertNull($this->message->getBody());

        $this->assertNoParameters();
    }

    public function testInitialState()
    {
        $this->message = $this->getMockBuilder('Ivory\HttpAdapter\Message\AbstractMessage')
            ->setConstructorArgs(array(
                $protocolVersion = MessageInterface::PROTOCOL_VERSION_1_0,
                array(
                    ' fOo '  => array(' bar ', ' baz '),
                    ' baT '  => array(' ban '),
                    ' Date ' => array(' Fri, 15 aug 2014 12:34:56 UTC '),
                ),
                $body = $this->getMock('Psr\Http\Message\StreamableInterface'),
                $parameters = array('foo' => 'bar')
            ))
            ->getMockForAbstractClass();

        $this->assertSame($protocolVersion, $this->message->getProtocolVersion());

        $this->assertTrue($this->message->hasHeaders());
        $this->assertSame(
            array(
                'fOo'  => array('bar', 'baz'),
                'baT'  => array('ban'),
                'Date' => array('Fri, 15 aug 2014 12:34:56 UTC'),
            ),
            $this->message->getHeaders()
        );

        $this->assertTrue($this->message->hasBody());
        $this->assertSame($body, $this->message->getBody());

        $this->assertParameters($parameters);
    }

    public function testSetParameters()
    {
        $this->message->setParameters($parameters = array('foo' => 'bar'));

        $this->assertParameters($parameters);
    }

    public function testAddParameters()
    {
        $this->message->setParameters(array('foo' => 'bar'));
        $this->message->addParameters(array('foo' => 'baz', 'bat' => 'bot'));

        $this->assertParameters(array('foo' => array('bar', 'baz'), 'bat' => 'bot'));
    }

    public function testRemoveParameters()
    {
        $this->message->setParameters($parameters = array('foo' => 'bar'));
        $this->message->removeParameters(array_keys($parameters));

        $this->assertNoParameters();
    }

    public function testClearParameters()
    {
        $this->message->setParameters(array('foo' => 'bar'));
        $this->message->clearParameters();

        $this->assertNoParameters();
    }

    public function testSetParameter()
    {
        $this->message->setParameter($name = 'foo', $value = 'bar');

        $this->assertParameter($name, $value);
    }

    public function testAddParameter()
    {
        $this->message->setParameter($name = 'foo', $value1 = 'bar');
        $this->message->addParameter($name, $value2 = 'baz');

        $this->assertParameter($name, array($value1, $value2));
    }

    public function testRemoveParameter()
    {
        $this->message->setParameter($name = 'foo', 'bar');
        $this->message->removeParameter($name);

        $this->assertNoParameter($name);
    }

    /**
     * Asserts there are the parameters.
     *
     * @param array $parameters The parameters.
     */
    protected function assertParameters(array $parameters)
    {
        $this->assertTrue($this->message->hasParameters());
        $this->assertSame($parameters, $this->message->getParameters());

        foreach ($parameters as $name => $value) {
            $this->assertParameter($name, $value);
        }
    }

    /**
     * Asserts there are no parameters.
     */
    protected function assertNoParameters()
    {
        $this->assertFalse($this->message->hasParameters());
        $this->assertEmpty($this->message->getParameters());
    }

    /**
     * Asserts there is the parameter.
     *
     * @param string $name  The parameter name.
     * @param mixed  $value The parameter value.
     */
    protected function assertParameter($name, $value)
    {
        $this->assertTrue($this->message->hasParameter($name));
        $this->assertSame($value, $this->message->getParameter($name));
    }

    /**
     * Asserts there is no parameter.
     *
     * @param string $name The parameter name.
     */
    protected function assertNoParameter($name)
    {
        $this->assertFalse($this->message->hasParameter($name));
        $this->assertNull($this->message->getParameter($name));
    }
}
