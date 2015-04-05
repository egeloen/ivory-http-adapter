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
use Ivory\HttpAdapter\Event\MultiRequestSentEvent;
use Ivory\HttpAdapter\Event\RequestSentEvent;
use Ivory\HttpAdapter\Event\Redirect\Redirect;
use Ivory\HttpAdapter\Event\Redirect\RedirectInterface;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\MultiHttpAdapterException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Redirect subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class RedirectSubscriber implements EventSubscriberInterface
{
    /** @var \Ivory\HttpAdapter\Event\Redirect\RedirectInterface */
    private $redirect;

    /**
     * Creates a redirect subscriber.
     *
     * @param \Ivory\HttpAdapter\Event\Redirect\RedirectInterface $redirect The redirect.
     */
    public function __construct(RedirectInterface $redirect = null)
    {
        $this->redirect = $redirect ?: new Redirect();
    }

    /**
     * Gets the redirect.
     *
     * @return \Ivory\HttpAdapter\Event\Redirect\RedirectInterface The redirect.
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * On request sent event.
     *
     * @param \Ivory\HttpAdapter\Event\RequestSentEvent $event The request sent event.
     */
    public function onRequestSent(RequestSentEvent $event)
    {
        try {
            $redirectRequest = $this->redirect->createRedirectRequest(
                $event->getResponse(),
                $event->getRequest(),
                $event->getHttpAdapter()
            );
        } catch (HttpAdapterException $e) {
            $event->setException($e);

            return;
        }

        if ($redirectRequest === false) {
            $event->setResponse($this->redirect->prepareResponse($event->getResponse(), $event->getRequest()));

            return;
        }

        try {
            $event->setResponse($event->getHttpAdapter()->sendRequest($redirectRequest));
        } catch (HttpAdapterException $e) {
            $event->setException($e);
        }
    }

    /**
     * On multi request sent event.
     *
     * @param \Ivory\HttpAdapter\Event\MultiRequestSentEvent $event The multi request sent event.
     */
    public function onMultiRequestSent(MultiRequestSentEvent $event)
    {
        $redirectRequests = array();

        foreach ($event->getResponses() as $response) {
            try {
                $redirectRequest = $this->redirect->createRedirectRequest(
                    $response,
                    $response->getParameter('request'),
                    $event->getHttpAdapter()
                );
            } catch (HttpAdapterException $e) {
                $event->removeResponse($response);
                $event->addException($e);
                continue;
            }

            if ($redirectRequest === false) {
                $event->removeResponse($response);
                $event->addResponse($this->redirect->prepareResponse($response, $response->getParameter('request')));
            } else {
                $redirectRequests[] = $redirectRequest;
                $event->removeResponse($response);
            }
        }

        if (empty($redirectRequests)) {
            return;
        }

        try {
            $event->addResponses($event->getHttpAdapter()->sendRequests($redirectRequests));
        } catch (MultiHttpAdapterException $e) {
            $event->addResponses($e->getResponses());
            $event->addExceptions($e->getExceptions());
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::REQUEST_SENT       => array('onRequestSent', 0),
            Events::MULTI_REQUEST_SENT => array('onMultiRequestSent', 0),
        );
    }
}
