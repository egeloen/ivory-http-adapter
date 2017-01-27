<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\History;

use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface JournalEntryFactoryInterface
{
    /**
     * @param InternalRequestInterface $request
     * @param ResponseInterface        $response
     *
     * @return JournalEntryInterface
     */
    public function create(InternalRequestInterface $request, ResponseInterface $response);
}
