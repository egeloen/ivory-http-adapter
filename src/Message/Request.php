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

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Zend\Diactoros\Request as DiactorosRequest;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Request extends DiactorosRequest implements RequestInterface
{
    use MessageTrait;

    /**
     * @param string|UriInterface|null        $uri
     * @param string|null                     $method
     * @param string|resource|StreamInterface $body
     * @param array                           $headers
     * @param array                           $parameters
     */
    public function __construct(
        $uri = null,
        $method = null,
        $body = 'php://memory',
        array $headers = [],
        array $parameters = []
    ) {
        parent::__construct($uri, $method, $body, $headers);

        $this->parameters = $parameters;
    }
}
