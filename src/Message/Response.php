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

use Zend\Diactoros\Response as DiactorosResponse;

/**
 * Response.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Response extends DiactorosResponse implements ResponseInterface
{
    use MessageTrait;

    /**
     * @param string|resource|\Psr\Http\Message\StreamInterface $body       The response body.
     * @param integer                                           $status     The response status code.
     * @param array                                             $headers    The response headers.
     * @param array                                             $parameters The response parameters.
     */
    public function __construct(
        $body = 'php://memory',
        $status = 200,
        array $headers = array(),
        array $parameters = array()
    ) {
        parent::__construct($body, $status, $headers);

        $this->parameters = $parameters;
    }
}
