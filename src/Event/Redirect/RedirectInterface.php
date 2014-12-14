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

use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * Redirect.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface RedirectInterface
{
    /** @const string The parent request parameter. */
    const PARENT_REQUEST = 'parent_request';

    /** @const string The redirect count parameter.  */
    const REDIRECT_COUNT = 'redirect_count';

    /** @const string The effective url parameter. */
    const EFFECTIVE_URL = 'effective_url';

    /**
     * Gets the maximum number of redirects.
     *
     * @return integer The maximum number of redirects.
     */
    public function getMax();

    /**
     * Sets the maximum number of redirects.
     *
     * @param integer $max The maximum number of redirects.
     */
    public function setMax($max);

    /**
     * Checks if it follows strictly the RFC.
     *
     * @return boolean TRUE if it follows strictly the RFC else FALSE.
     */
    public function isStrict();

    /**
     * Sets if it follows strictly the RFC.
     *
     * @param boolean $strict TRUE if it follows strictly the RFC else FALSE.
     */
    public function setStrict($strict);

    /**
     * Checks if it throws an exception when the max redirects is exceeded.
     *
     * @return boolean TRUE if it throws an exception when the max redirects is exceeded else FALSE.
     */
    public function getThrowException();

    /**
     * Sets if it throws an exception when the max redirects is exceeded.
     *
     * @param boolean $throwException TRUE if it throws an exception when the max redirects is exceeded else FALSE.
     */
    public function setThrowException($throwException);

    /**
     * Redirects a response.
     *
     * @param \Ivory\HttpAdapter\Message\ResponseInterface        $response        The response.
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     * @param \Ivory\HttpAdapter\HttpAdapterInterface             $httpAdapter     The http adapter.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occured.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The redirected response.
     */
    public function redirect(
        ResponseInterface $response,
        InternalRequestInterface $internalRequest,
        HttpAdapterInterface $httpAdapter
    );
}
