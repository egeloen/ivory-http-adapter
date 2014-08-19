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
    protected $maxRedirects;

    /** @var boolean */
    protected $throwException;

    /**
     * Creates a redirect subscriber.
     *
     * @param integer $maxRedirects   The maximum redirects.
     * @param boolean $throwException TRUE if it throws an exception when the max redirects is exceeded else FALSE.
     */
    public function __construct($maxRedirects = 5, $throwException = true)
    {
        $this->setMaxRedirects($maxRedirects);
        $this->setThrowException($throwException);
    }

    /**
     * Gets the maximum redirects.
     *
     * @return integer The maximum redirects.
     */
    public function getMaxRedirects()
    {
        return $this->maxRedirects;
    }

    /**
     * Sets the maximum redirects.
     *
     * @param integer $maxRedirects The maximum redirects.
     */
    public function setMaxRedirects($maxRedirects)
    {
        $this->maxRedirects = $maxRedirects;
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
            return $this->populateResponse($request, $response);
        }

        $redirectCount = $request->getParameter(self::REDIRECT_COUNT) + 1;

        if ($redirectCount > $this->maxRedirects) {
            if ($this->throwException) {
                throw HttpAdapterException::maxRedirectsExceeded(
                    $this->getRootRequest($request)->getUrl(),
                    $this->maxRedirects,
                    $httpAdapter->getName()
                );
            }

            return $this->populateResponse($request, $response);
        }

        $redirect = $httpAdapter->getMessageFactory()->cloneInternalRequest($request);
        $redirect->setUrl($response->getHeader('Location'));
        $redirect->setParameter(self::PARENT_REQUEST, $request);
        $redirect->setParameter(self::REDIRECT_COUNT, $redirectCount);

        $event->setResponse($httpAdapter->sendInternalRequest($redirect));
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(Events::POST_SEND => 'onPostSend');
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
     * Populates the response.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request  The request.
     * @param \Ivory\HttpAdapter\Message\ResponseInterface        $response The response.
     */
    protected function populateResponse(InternalRequestInterface $request, ResponseInterface $response)
    {
        $response->setParameter(self::REDIRECT_COUNT, (int) $request->getParameter(self::REDIRECT_COUNT));
        $response->setParameter(self::EFFECTIVE_URL, $request->getUrl());
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
