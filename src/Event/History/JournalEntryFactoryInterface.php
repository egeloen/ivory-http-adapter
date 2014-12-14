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
 * Journal entry factory.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface JournalEntryFactoryInterface
{
    /**
     * Creates a journal entry.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request  The request.
     * @param \Ivory\HttpAdapter\Message\ResponseInterface        $response The response.
     *
     * @return \Ivory\HttpAdapter\Event\History\JournalEntryInterface The journal entry.
     */
    public function create(InternalRequestInterface $request, ResponseInterface $response);
}
