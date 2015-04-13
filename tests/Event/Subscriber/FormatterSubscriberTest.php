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

/**
 * Formatter subscriber test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class FormatterSubscriberTest extends AbstractSubscriberTest
{
    /** @var \Ivory\HttpAdapter\Event\Subscriber\AbstractFormatterSubscriber|\PHPUnit_Framework_MockObject_MockObject */
    private $formatterSubscriber;

    /** @var \Zend\Log\Formatter\FormatterInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $formatter;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->formatterSubscriber = $this->createFormatterSubscriberMockBuilder()
            ->setConstructorArgs(array($this->formatter = $this->createFormatterMock()))
            ->getMockForAbstractClass();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->formatter);
        unset($this->formatterSubscriber);
    }

    public function testDefaultState()
    {
        $this->formatterSubscriber = $this->createFormatterSubscriberMockBuilder()->getMockForAbstractClass();

        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Formatter\Formatter',
            $this->formatterSubscriber->getFormatter()
        );
    }

    public function testInitialState()
    {
        $this->assertSame($this->formatter, $this->formatterSubscriber->getFormatter());
    }

    /**
     * Creates a formatter subscriber mock builder.
     *
     * @return \Ivory\HttpAdapter\Event\Subscriber\AbstractFormatterSubscriber|\PHPUnit_Framework_MockObject_MockObject The formatter subscriber mock builder.
     */
    private function createFormatterSubscriberMockBuilder()
    {
        return $this->getMockBuilder('Ivory\HttpAdapter\Event\Subscriber\AbstractFormatterSubscriber');
    }

    /**
     * Creates a formatter mock.
     *
     * @return \Ivory\HttpAdapter\Event\Formatter\FormatterInterface|\PHPUnit_Framework_MockObject_MockObject The formatter mock.
     */
    private function createFormatterMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Event\Formatter\FormatterInterface');
    }
}
