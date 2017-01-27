<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\Subscriber;

use Ivory\HttpAdapter\Event\Formatter\Formatter;
use Ivory\HttpAdapter\Event\Formatter\FormatterInterface;
use Ivory\HttpAdapter\Event\Timer\TimerInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractFormatterSubscriber extends AbstractTimerSubscriber
{
    /**
     * @var FormatterInterface
     */
    private $formatter;

    /**
     * @param FormatterInterface|null $formatter
     * @param TimerInterface|null     $timer
     */
    public function __construct(FormatterInterface $formatter = null, TimerInterface $timer = null)
    {
        parent::__construct($timer);

        $this->formatter = $formatter ?: new Formatter();
    }

    /**
     * @return FormatterInterface
     */
    public function getFormatter()
    {
        return $this->formatter;
    }
}
