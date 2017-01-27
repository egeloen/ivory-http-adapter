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
use Zend\Diactoros\Response as DiactorosResponse;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Response extends DiactorosResponse implements ResponseInterface
{
    use MessageTrait;

    /**
     * @param string|resource|StreamInterface $body
     * @param int                             $status
     * @param array                           $headers
     * @param array                           $parameters
     */
    public function __construct(
        $body = 'php://memory',
        $status = 200,
        array $headers = [],
        array $parameters = []
    ) {
        parent::__construct($body, $status, $headers);

        $this->parameters = $parameters;
    }
}
