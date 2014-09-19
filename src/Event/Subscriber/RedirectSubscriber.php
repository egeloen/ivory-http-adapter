<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\Subscriber;

use Ivory\HttpAdapter\Event\Events;
use Ivory\HttpAdapter\Event\PostSendEvent;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Redirect subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class RedirectSubscriber implements EventSubscriberInterface
{
    /** @const string */
    const PARENT_REQUEST = 'parent_request';

    /** @const string */
    const REDIRECT_COUNT = 'redirect_count';

    /** @const string */
    const EFFECTIVE_URL = 'effective_url';

    /** @var integer */
    protected $max;

    /** @var boolean */
    protected $strict;

    /** @var boolean */
    protected $throwException;

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
     * Gets the maximum number of redirects.
     *
     * @return integer The maximum number of redirects.
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Sets the maximum number of redirects.
     *
     * @param integer $max The maximum number of redirects.
     */
    public function setMax($max)
    {
        $this->max = $max;
    }

    /**
     * Checks if it follows strictly the RFC.
     *
     * @return boolean TRUE if it follows strictly the RFC else FALSE.
     */
    public function isStrict()
    {
        return $this->strict;
    }

    /**
     * Sets if it follows strictly the RFC.
     *
     * @param boolean $strict TRUE if it follows strictly the RFC else FALSE.
     */
    public function setStrict($strict)
    {
        $this->strict = $strict;
    }

    /**
     * Checks if it throws an exception when the max redirects is exceeded.
     *
     * @return boolean TRUE if it throws an exception when the max redirects is exceeded else FALSE.
     */
    public function getThrowException()
    {
        return $this->throwException;
    }

    /**
     * Sets if it throws an exception when the max redirects is exceeded.
     *
     * @param boolean $throwException TRUE if it throws an exception when the max redirects is exceeded else FALSE.
     */
    public function setThrowException($throwException)
    {
        $this->throwException = $throwException;
    }

    /**
     * On post send event.
     *
     * @param \Ivory\HttpAdapter\Event\PostSendEvent $event The event.
     */
    public function onPostSend(PostSendEvent $event)
    {
        $httpAdapter = $event->getHttpAdapter();
        $request = $event->getRequest();
        $response = $event->getResponse();

        if (!$this->isRedirect($response)) {
            return $this->prepareResponse($request, $response);
        }

        if ($request->getParameter(self::REDIRECT_COUNT) + 1 > $this->max) {
            if ($this->throwException) {
                throw HttpAdapterException::maxRedirectsExceeded(
                    (string) $this->getRootRequest($request)->getUrl(),
                    $this->max,
                    $httpAdapter->getName()
                );
            }

            return $this->prepareResponse($request, $response);
        }

        $redirectRequest = $this->prepareRedirectRequest($httpAdapter, $request, $response);
        $redirectResponse = $httpAdapter->sendInternalRequest($redirectRequest);

        $event->setResponse($redirectResponse);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(Events::POST_SEND => array('onPostSend', 0));
    }

    /**
     * Checks if the response is a redirect.
     *
     * @param \Ivory\HttpAdapter\Message\ResponseInterface $response The response.
     *
     * @return boolean TRUE if the response is a redirect else FALSE.
     */
    protected function isRedirect(ResponseInterface $response)
    {
        return $response->getStatusCode() >= 300
            && $response->getStatusCode() < 400
            && $response->hasHeader('Location');
    }

    /**
     * Prepares the redirect request.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface             $httpAdapter The http adapter.
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request     The request.
     * @param \Ivory\HttpAdapter\Message\ResponseInterface        $response    The response.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The prepared redirect request.
     */
    protected function prepareRedirectRequest(
        HttpAdapterInterface $httpAdapter,
        InternalRequestInterface $request,
        ResponseInterface $response
    ) {
        $redirect = $httpAdapter->getConfiguration()->getMessageFactory()->cloneInternalRequest($request);

        if ($response->getStatusCode() === 303 || (!$this->strict && $response->getStatusCode() <= 302)) {
            $redirect->setMethod(InternalRequestInterface::METHOD_GET);
            $redirect->removeHeaders(array('Content-Type', 'Content-Length'));
            $redirect->clearRawDatas();
            $redirect->clearDatas();
            $redirect->clearFiles();
        }

        $redirect->setUrl($response->getHeader('Location'));
        $redirect->setParameter(self::PARENT_REQUEST, $request);
        $redirect->setParameter(self::REDIRECT_COUNT, $request->getParameter(self::REDIRECT_COUNT) + 1);

        return $redirect;
    }

    /**
     * Prepares the response.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request  The request.
     * @param \Ivory\HttpAdapter\Message\ResponseInterface        $response The response.
     */
    protected function prepareResponse(InternalRequestInterface $request, ResponseInterface $response)
    {
        $response->setParameter(self::REDIRECT_COUNT, (int) $request->getParameter(self::REDIRECT_COUNT));
        $response->setParameter(self::EFFECTIVE_URL, (string) $request->getUrl());
    }

    /**
     * Gets the root request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request The request.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The root request.
     */
    protected function getRootRequest(InternalRequestInterface $request)
    {
        $root = $request;

        while ($root->hasParameter(self::PARENT_REQUEST)) {
            $root = $root->getParameter(self::PARENT_REQUEST);
        }

        return $root;
    }
}
