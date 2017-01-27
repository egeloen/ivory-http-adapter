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

use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\MessageFactoryInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface CacheInterface
{
    /**
     * @param InternalRequestInterface $internalRequest
     * @param MessageFactoryInterface  $messageFactory
     *
     * @return ResponseInterface|null
     */
    public function getResponse(InternalRequestInterface $internalRequest, MessageFactoryInterface $messageFactory);

    /**
     * @param InternalRequestInterface $internalRequest
     * @param MessageFactoryInterface  $messageFactory
     *
     * @return HttpAdapterException|null
     */
    public function getException(InternalRequestInterface $internalRequest, MessageFactoryInterface $messageFactory);

    /**
     * @param ResponseInterface        $response
     * @param InternalRequestInterface $internalRequest
     */
    public function saveResponse(ResponseInterface $response, InternalRequestInterface $internalRequest);

    /**
     * @param HttpAdapterException     $exception
     * @param InternalRequestInterface $internalRequest
     */
    public function saveException(HttpAdapterException $exception, InternalRequestInterface $internalRequest);
}
