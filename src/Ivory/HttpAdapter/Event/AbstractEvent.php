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

use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Abstract event.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractEvent extends Event
{
    /** @var \Ivory\HttpAdapter\Message\InternalRequestInterface */
    protected $request;

    /**
     * Creates a pre send event.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request The request.
     */
    public function __construct(InternalRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Gets the request.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The request.
     */
    public function getRequest()
    {
        return $this->request;
    }
}
