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
 * {@inheritdoc}
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class JournalEntryFactory implements JournalEntryFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(InternalRequestInterface $request, ResponseInterface $response)
    {
        return new JournalEntry($request, $response);
    }
}
