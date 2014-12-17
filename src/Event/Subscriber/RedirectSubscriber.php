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
use Ivory\HttpAdapter\Event\Redirect\Redirect;
use Ivory\HttpAdapter\Event\Redirect\RedirectInterface;
use Ivory\HttpAdapter\HttpAdapterException;
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
        $this->setRedirect($redirect ?: new Redirect());
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
     * Sets the redirect.
     *
     * @param \Ivory\HttpAdapter\Event\Redirect\RedirectInterface $redirect The redirect.
     */
    public function setRedirect(RedirectInterface $redirect)
    {
        $this->redirect = $redirect;
    }

    /**
     * On post send event.
     *
     * @param \Ivory\HttpAdapter\Event\PostSendEvent $event The event.
     */
    public function onPostSend(PostSendEvent $event)
    {
        if (!$this->redirect->isRedirectResponse($event->getResponse())) {
            $this->redirect->prepareResponse($event->getResponse(), $event->getRequest());

            return;
        }

        if ($this->redirect->isMaxRedirectRequest($event->getRequest())) {
            if ($this->redirect->getThrowException()) {
                throw HttpAdapterException::maxRedirectsExceeded(
                    (string) $this->redirect->getRootRequest($event->getRequest())->getUrl(),
                    $this->redirect->getMax(),
                    $event->getHttpAdapter()->getName()
                );
            }

            $this->redirect->prepareResponse($event->getResponse(), $event->getRequest());

            return;
        }

        try {
            $event->setResponse($event->getHttpAdapter()->sendRequest($this->redirect->createRedirectRequest(
                $event->getResponse(),
                $event->getRequest(),
                $event->getHttpAdapter()->getConfiguration()->getMessageFactory()
            )));
        } catch (HttpAdapterException $e) {
            $event->setException($e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(Events::POST_SEND => array('onPostSend', 0));
    }
}
