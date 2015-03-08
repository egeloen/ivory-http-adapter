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
use Symfony\Component\EventDispatcher\Event;

/**
 * Abstract event.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractEvent extends Event
{
    /** @var \Ivory\HttpAdapter\HttpAdapterInterface */
    private $httpAdapter;

    /**
     * Creates an event.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface $httpAdapter The http adapter.
     */
    public function __construct(HttpAdapterInterface $httpAdapter)
    {
        $this->httpAdapter = $httpAdapter;
    }

    /**
     * Gets the http adapter.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterInterface The http adapter.
     */
    public function getHttpAdapter()
    {
        return $this->httpAdapter;
    }
}
