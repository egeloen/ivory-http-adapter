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
use Ivory\HttpAdapter\Message\MessageFactoryInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

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
    public function redirect(
        ResponseInterface $response,
        InternalRequestInterface $internalRequest,
        HttpAdapterInterface $httpAdapter
    ) {
        if ($response->getStatusCode() < 300
            || $response->getStatusCode() >= 400
            || !$response->hasHeader('Location')
        ) {
            return $this->prepareResponse($response, $internalRequest);
        }

        if ($internalRequest->getParameter(self::REDIRECT_COUNT) >= $this->max) {
            if ($this->throwException) {
                throw HttpAdapterException::maxRedirectsExceeded(
                    (string) $this->getRootRequest($internalRequest)->getUrl(),
                    $this->max,
                    $httpAdapter->getName()
                );
            }

            return $this->prepareResponse($response, $internalRequest);
        }

        return $httpAdapter->sendRequest($this->prepareRedirectRequest(
            $response,
            $internalRequest,
            $httpAdapter->getConfiguration()->getMessageFactory()
        ));
    }

    /**
     * Prepares a redirect request.
     *
     * @param \Ivory\HttpAdapter\Message\ResponseInterface        $response        The response.
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     * @param \Ivory\HttpAdapter\Message\MessageFactoryInterface  $messageFactory  The message factory.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The prepared redirect request.
     */
    private function prepareRedirectRequest(
        ResponseInterface $response,
        InternalRequestInterface $internalRequest,
        MessageFactoryInterface $messageFactory
    ) {
        $redirect = $messageFactory->cloneInternalRequest($internalRequest);

        if ($response->getStatusCode() === 303 || (!$this->strict && $response->getStatusCode() <= 302)) {
            $redirect->setMethod(InternalRequestInterface::METHOD_GET);
            $redirect->removeHeaders(array('Content-Type', 'Content-Length'));
            $redirect->clearRawDatas();
            $redirect->clearDatas();
            $redirect->clearFiles();
        }

        $redirect->setUrl($response->getHeader('Location'));
        $redirect->setParameter(self::PARENT_REQUEST, $internalRequest);
        $redirect->setParameter(self::REDIRECT_COUNT, $internalRequest->getParameter(self::REDIRECT_COUNT) + 1);

        return $redirect;
    }

    /**
     * Prepares a response.
     *
     * @param \Ivory\HttpAdapter\Message\ResponseInterface        $response        The response.
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The prepared response.
     */
    private function prepareResponse(ResponseInterface $response, InternalRequestInterface $internalRequest)
    {
        $response->setParameter(self::REDIRECT_COUNT, (int) $internalRequest->getParameter(self::REDIRECT_COUNT));
        $response->setParameter(self::EFFECTIVE_URL, (string) $internalRequest->getUrl());

        return $response;
    }

    /**
     * Gets the root request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The root request.
     */
    private function getRootRequest(InternalRequestInterface $internalRequest)
    {
        $root = $internalRequest;

        while ($root->hasParameter(self::PARENT_REQUEST)) {
            $root = $root->getParameter(self::PARENT_REQUEST);
        }

        return $root;
    }
}
