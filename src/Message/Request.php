<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Message;

use Phly\Http\Request as PhlyRequest;

/**
 * Request.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Request extends PhlyRequest implements RequestInterface
{
    use MessageTrait;

    /**
     * @param null|string|\Psr\Http\Message\UriInterface            $uri        The request uri.
     * @param null|string                                           $method     The request method.
     * @param string|resource|\Psr\Http\Message\StreamableInterface $body       The request body.
     * @param array                                                 $headers    The request headers.
     * @param array                                                 $parameters The request parameters.
     */
    public function __construct(
        $uri = null,
        $method = null,
        $body = 'php://memory',
        array $headers = array(),
        array $parameters = array()
    ) {
        parent::__construct($uri, $method, $body, $headers);

        $this->parameters = $parameters;
    }
}
