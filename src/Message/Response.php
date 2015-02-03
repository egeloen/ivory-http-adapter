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

use Psr\Http\Message\StreamableInterface;

/**
 * Response.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Response extends AbstractMessage implements ResponseInterface
{
    /** @var integer */
    private $statusCode;

    /** @var string */
    private $reasonPhrase;

    /**
     * Creates a response.
     *
     * @param integer                                    $statusCode      The status code.
     * @param string                                     $reasonPhrase    The reason phrase.
     * @param string                                     $protocolVersion The protocol version.
     * @param array                                      $headers         The headers.
     * @param \Psr\Http\Message\StreamableInterface|null $body            The body.
     * @param array                                      $parameters      The parameters.
     */
    public function __construct(
        $statusCode = 200,
        $reasonPhrase = 'OK',
        $protocolVersion = self::PROTOCOL_VERSION_1_1,
        array $headers = array(),
        StreamableInterface $body = null,
        array $parameters = array()
    ) {
        parent::__construct($protocolVersion, $headers, $body, $parameters);

        $this->statusCode = $statusCode;
        $this->reasonPhrase = $reasonPhrase;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }
}
