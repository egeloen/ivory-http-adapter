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

use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\HttpAdapterInterface;

/**
 * Redirect.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Redirect implements RedirectInterface
{
    /** @var integer */
    private $max;

    /** @var boolean */
    private $strict;

    /** @var boolean */
    private $throwException;

    /**
     * Creates a redirect subscriber.
     *
     * @param integer $max            The maximum number of redirects.
     * @param boolean $strict         TRUE if it follows strictly the RFC else FALSE.
     * @param boolean $throwException TRUE if it throws an exception when the max redirects is exceeded else FALSE.
     */
    public function __construct($max = 5, $strict = false, $throwException = true)
    {
        $this->setMax($max);
        $this->setStrict($strict);
        $this->setThrowException($throwException);
    }

    /**
     * {@inheritdoc}
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * {@inheritdoc}
     */
    public function setMax($max)
    {
        $this->max = $max;
    }

    /**
     * {@inheritdoc}
     */
    public function isStrict()
    {
        return $this->strict;
    }

    /**
     * {@inheritdoc}
     */
    public function setStrict($strict)
    {
        $this->strict = $strict;
    }

    /**
     * {@inheritdoc}
     */
    public function getThrowException()
    {
        return $this->throwException;
    }

    /**
     * {@inheritdoc}
     */
    public function setThrowException($throwException)
    {
        $this->throwException = $throwException;
    }

    /**
     * {@inheritdoc}
     */
    public function createRedirectRequest(
        ResponseInterface $response,
        InternalRequestInterface $internalRequest,
        HttpAdapterInterface $httpAdapter
    ) {
        if ($response->getStatusCode() < 300
            || $response->getStatusCode() >= 400
            || !$response->hasHeader('Location')) {
            return false;
        }

        if ($internalRequest->getParameter(self::REDIRECT_COUNT) >= $this->max) {
            if ($this->throwException) {
                $rootRequest = $this->getRootRequest($internalRequest);
                $exception = HttpAdapterException::maxRedirectsExceeded(
                    (string) $rootRequest->getUri(),
                    $this->max,
                    $httpAdapter->getName()
                );

                $exception->setRequest($rootRequest);

                throw $exception;
            }

            return false;
        }

        $strict = $response->getStatusCode() === 303 || (!$this->strict && $response->getStatusCode() <= 302);

        $headers = $internalRequest->getHeaders();

        foreach ($headers as $key => $value) {
            if (strtolower($key) === 'host') {
                unset($headers[$key]);
            }
        }

        $redirect = $httpAdapter->getConfiguration()->getMessageFactory()->createInternalRequest(
            $response->getHeaderLine('Location'),
            $strict ? InternalRequestInterface::METHOD_GET : $internalRequest->getMethod(),
            $internalRequest->getProtocolVersion(),
            $headers,
            $strict ? array() : $internalRequest->getDatas(),
            $strict ? array() : $internalRequest->getFiles(),
            $internalRequest->getParameters()
        );

        if ($strict) {
            $redirect = $redirect->withoutHeader('Content-Type')->withoutHeader('Content-Length');
        } else {
            $redirect = $redirect->withBody($internalRequest->getBody());
        }

        return $redirect
            ->withParameter(self::PARENT_REQUEST, $internalRequest)
            ->withParameter(self::REDIRECT_COUNT, $internalRequest->getParameter(self::REDIRECT_COUNT) + 1);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareResponse(ResponseInterface $response, InternalRequestInterface $internalRequest)
    {
        return $response
            ->withParameter(self::REDIRECT_COUNT, (int) $internalRequest->getParameter(self::REDIRECT_COUNT))
            ->withParameter(self::EFFECTIVE_URI, (string) $internalRequest->getUri());
    }

    /**
     * {@inheritdoc}
     */
    private function getRootRequest(InternalRequestInterface $internalRequest)
    {
        if ($internalRequest->hasParameter(self::PARENT_REQUEST)) {
            return $this->getRootRequest($internalRequest->getParameter(self::PARENT_REQUEST));
        }

        return $internalRequest;
    }
}
