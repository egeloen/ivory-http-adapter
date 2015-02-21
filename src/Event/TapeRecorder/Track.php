<?php

/**
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\TapeRecorder;

use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\RequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * Track
 *
 * @author Jérôme Gamez <jerome@gamez.name>
 */
class Track implements TrackInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var HttpAdapterException
     */
    private $exception;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function hasResponse()
    {
        return $this->response !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        if (!$this->hasResponse()) {
            return null;
        }

        if ($this->response->hasBody()) {
            $this->response->getBody()->seek(0, SEEK_SET);
        }

        return $this->response;
    }

    /**
     * {@inheritdoc}
     */
    public function setResponse(ResponseInterface $response = null)
    {
        $this->response = $response;
    }

    /**
     * {@inheritdoc}
     */
    public function hasException()
    {
        return $this->exception !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getException()
    {
        if (!$this->hasException()) {
            return null;
        }

        if ($this->exception->hasResponse() && $this->exception->getResponse()->hasBody()) {
            $this->exception->getResponse()->getBody()->seek(0, SEEK_SET);
        }

        return $this->exception;
    }

    /**
     * {@inheritdoc}
     */
    public function setException(HttpAdapterException $exception = null)
    {
        $this->exception = $exception;
    }
}
