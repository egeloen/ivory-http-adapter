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

use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * Exception event.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ExceptionEvent extends AbstractEvent
{
    /** @var \Ivory\HttpAdapter\HttpAdapterException */
    protected $exception;

    /**
     * Creates an exception event.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request   The request.
     * @param \Ivory\HttpAdapter\HttpAdapterException             $exception The exception.
     */
    public function __construct(InternalRequestInterface $request, HttpAdapterException $exception)
    {
        parent::__construct($request);

        $this->exception = $exception;
    }

    /**
     * Gets the exception.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException The exception.
     */
    public function getException()
    {
        return $this->exception;
    }
}
