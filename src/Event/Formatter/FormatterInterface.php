<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\Formatter;

use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface FormatterInterface
{
    /**
     * @param InternalRequestInterface $request
     *
     * @return array
     */
    public function formatRequest(InternalRequestInterface $request);

    /**
     * @param ResponseInterface $response
     *
     * @return array
     */
    public function formatResponse(ResponseInterface $response);

    /**
     * @param HttpAdapterException $exception
     *
     * @return array
     */
    public function formatException(HttpAdapterException $exception);
}
