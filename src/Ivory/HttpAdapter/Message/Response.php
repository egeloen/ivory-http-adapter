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

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * Response.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Response extends AbstractMessage implements PsrResponseInterface, ResponseInterface
{
    /** @var integer */
    protected $statusCode;

    /** @var string */
    protected $reasonPhrase;

    /** @var string */
    protected $effectiveUrl;

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
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * {@inheritdoc}
     */
    public function setReasonPhrase($reasonPhrase)
    {
        $this->reasonPhrase = $reasonPhrase;
    }

    /**
     * {@inheritdoc}
     */
    public function getEffectiveUrl()
    {
        return $this->effectiveUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setEffectiveUrl($effectiveUrl)
    {
        $this->effectiveUrl = $effectiveUrl;
    }
}
