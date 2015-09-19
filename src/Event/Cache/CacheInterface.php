<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\Cache;

use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\MessageFactoryInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Ivory\HttpAdapter\HttpAdapterException;

/**
 * Cache.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface CacheInterface
{
    /**
     * Gets a response.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest
     * @param \Ivory\HttpAdapter\Message\MessageFactoryInterface  $messageFactory
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface|null
     */
    public function getResponse(InternalRequestInterface $internalRequest, MessageFactoryInterface $messageFactory);

    /**
     * Gets an exception.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest
     * @param \Ivory\HttpAdapter\Message\MessageFactoryInterface  $messageFactory
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException|null
     */
    public function getException(InternalRequestInterface $internalRequest, MessageFactoryInterface $messageFactory);

    /**
     * Saves a response.
     *
     * @param \Ivory\HttpAdapter\Message\ResponseInterface        $response
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest
     */
    public function saveResponse(ResponseInterface $response, InternalRequestInterface $internalRequest);

    /**
     * Saves an exception.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterException             $exception
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest
     */
    public function saveException(HttpAdapterException $exception, InternalRequestInterface $internalRequest);
}
