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
 * Abstract formatter subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractFormatterSubscriber extends AbstractTimerSubscriber
{
    /** @var \Ivory\HttpAdapter\Event\Formatter\FormatterInterface */
    private $formatter;

    /**
     * Creates a formatter subscriber.
     *
     * @param \Ivory\HttpAdapter\Event\Formatter\FormatterInterface|null $formatter The formatter.
     * @param \Ivory\HttpAdapter\Event\Timer\TimerInterface|null         $timer     The timer.
     */
    public function __construct(FormatterInterface $formatter = null, TimerInterface $timer = null)
    {
        parent::__construct($timer);

        $this->formatter = $formatter ?: new Formatter();
    }

    /**
     * Gets the formatter.
     *
     * @return \Ivory\HttpAdapter\Event\Formatter\FormatterInterface The formatter.
     */
    public function getFormatter()
    {
        return $this->formatter;
    }
}
