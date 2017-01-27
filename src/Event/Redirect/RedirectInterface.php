<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\Redirect;

use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface RedirectInterface
{
    const PARENT_REQUEST = 'parent_request';
    const REDIRECT_COUNT = 'redirect_count';
    const EFFECTIVE_URI = 'effective_uri';

    /**
     * @return int
     */
    public function getMax();

    /**
     * @param int $max
     */
    public function setMax($max);

    /**
     * @return bool
     */
    public function isStrict();

    /**
     * @param bool $strict
     */
    public function setStrict($strict);

    /**
     * @return bool
     */
    public function getThrowException();

    /**
     * @param bool $throwException
     */
    public function setThrowException($throwException);

    /**
     * @param ResponseInterface        $response
     * @param InternalRequestInterface $internalRequest
     * @param HttpAdapterInterface     $httpAdapter
     *
     * @throws HttpAdapterException
     *
     * @return InternalRequestInterface|false
     */
    public function createRedirectRequest(
        ResponseInterface $response,
        InternalRequestInterface $internalRequest,
        HttpAdapterInterface $httpAdapter
    );

    /**
     * @param ResponseInterface        $response
     * @param InternalRequestInterface $internalRequest
     *
     * @return ResponseInterface
     */
    public function prepareResponse(ResponseInterface $response, InternalRequestInterface $internalRequest);
}
