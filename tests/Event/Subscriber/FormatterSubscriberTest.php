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

use Ivory\HttpAdapter\Event\Formatter\FormatterInterface;
use Ivory\HttpAdapter\Event\Subscriber\AbstractFormatterSubscriber;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FormatterSubscriberTest extends AbstractSubscriberTest
{
    /**
     * @var AbstractFormatterSubscriber|\PHPUnit_Framework_MockObject_MockObject
     */
    private $formatterSubscriber;

    /**
     * @var FormatterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $formatter;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->formatterSubscriber = $this->createFormatterSubscriberMockBuilder()
            ->setConstructorArgs([$this->formatter = $this->createFormatterMock()])
            ->getMockForAbstractClass();
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
     * @return AbstractFormatterSubscriber|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createFormatterSubscriberMockBuilder()
    {
        return $this->getMockBuilder('Ivory\HttpAdapter\Event\Subscriber\AbstractFormatterSubscriber');
    }

    /**
     * @return FormatterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createFormatterMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Event\Formatter\FormatterInterface');
    }
}
