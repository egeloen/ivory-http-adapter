<?php

/**
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter;

use Ivory\HttpAdapter\Message\MessageFactory;
use Ivory\HttpAdapter\Message\MessageFactoryInterface;
use Ivory\HttpAdapter\Message\MessageInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * {@inheritdoc}
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /** @var \Ivory\HttpAdapter\Message\MessageFactoryInterface */
    private $messageFactory;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface|null */
    private $eventDispatcher;

    /** @var string */
    private $protocolVersion = MessageInterface::PROTOCOL_VERSION_1_1;

    /** @var boolean */
    private $keepAlive = false;

    /** @var string|null */
    private $encodingType;

    /** @var string */
    private $boundary;

    /** @var float */
    private $timeout = 10;

    /** @var string */
    private $userAgent;

    /**
     * Creates an http adapter.
     *
     * @param \Ivory\HttpAdapter\Message\MessageFactoryInterface|null          $messageFactory  The message factory.
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface|null $eventDispatcher The event dispatcher.
     */
    public function __construct(
        MessageFactoryInterface $messageFactory = null,
        EventDispatcherInterface $eventDispatcher = null
    ) {
        if (!$eventDispatcher && class_exists('Symfony\Component\EventDispatcher\EventDispatcher')) {
            $eventDispatcher = new EventDispatcher();
        }

        $this->setMessageFactory($messageFactory ?: new MessageFactory());
        $this->setEventDispatcher($eventDispatcher);
        $this->setBoundary(sha1(microtime()));
        $this->setUserAgent('Ivory Http Adapter '.HttpAdapterInterface::VERSION);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageFactory()
    {
        return $this->messageFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function setMessageFactory(MessageFactoryInterface $factory)
    {
        $this->messageFactory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function hasEventDispatcher()
    {
        return $this->eventDispatcher !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function setProtocolVersion($protocolVersion)
    {
        $this->protocolVersion = $protocolVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function getKeepAlive()
    {
        return $this->keepAlive;
    }

    /**
     * {@inheritdoc}
     */
    public function setKeepAlive($keepAlive)
    {
        $this->keepAlive = $keepAlive;
    }

    /**
     * {@inheritdoc}
     */
    public function hasEncodingType()
    {
        return $this->encodingType !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getEncodingType()
    {
        return $this->encodingType;
    }

    /**
     * {@inheritdoc}
     */
    public function setEncodingType($encodingType)
    {
        $this->encodingType = $encodingType;
    }

    /**
     * {@inheritdoc}
     */
    public function getBoundary()
    {
        return $this->boundary;
    }

    /**
     * {@inheritdoc}
     */
    public function setBoundary($boundary)
    {
        $this->boundary = $boundary;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * {@inheritdoc}
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * {@inheritdoc}
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    /**
     * {@inheritdoc}
     */
    public function hasBaseUrl()
    {
        return $this->messageFactory->hasBaseUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseUrl($baseUrl)
    {
        $this->messageFactory->setBaseUrl($baseUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseUrl()
    {
        return $this->messageFactory->getBaseUrl();
    }
}
