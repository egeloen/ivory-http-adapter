<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event;

use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\MultiHttpAdapterException;

/**
 * Multi exception event.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MultiExceptionEvent extends AbstractEvent
{
    /** @var \Ivory\HttpAdapter\MultiHttpAdapterException */
    private $exception;

    /**
     * Creates an exception event.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface      $httpAdapter The http adapter.
     * @param \Ivory\HttpAdapter\MultiHttpAdapterException $exception   The exception.
     */
    public function __construct(HttpAdapterInterface $httpAdapter, MultiHttpAdapterException $exception)
    {
        parent::__construct($httpAdapter);

        $this->setException($exception);
    }

    /**
     * Gets the exception.
     *
     * @return \Ivory\HttpAdapter\MultiHttpAdapterException The exception.
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Sets the exception.
     *
     * @param \Ivory\HttpAdapter\MultiHttpAdapterException $exception The exception.
     */
    public function setException(MultiHttpAdapterException $exception)
    {
        $this->exception = $exception;
    }
}
