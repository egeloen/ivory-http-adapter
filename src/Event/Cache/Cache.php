<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\Cache;

use Ivory\HttpAdapter\Event\Formatter\Formatter;
use Ivory\HttpAdapter\Event\Formatter\FormatterInterface;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\MessageFactoryInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Ivory\HttpAdapter\Event\Cache\Adapter\CacheAdapterInterface;

/**
 * Cache.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Cache implements CacheInterface
{
    /** @var \Ivory\HttpAdapter\Event\Cache\Adapter\CacheAdapterInterface */
    private $adapter;

    /** @var \Ivory\HttpAdapter\Event\Formatter\FormatterInterface */
    private $formatter;

    /** @var integer|null */
    private $lifetime;

    /** @var boolean */
    private $cacheException;

    /**
     * Creates a cache.
     *
     * @param \Ivory\HttpAdapter\Event\Cache\Adapter\CacheAdapterInterface  $adapter        The adapter.
     * @param \Ivory\HttpAdapter\Event\Formatter\FormatterInterface|null    $formatter      The formatter.
     * @param integer|null                                                  $lifetime       The lifetime.
     * @param boolean                                                       $cacheException TRUE if the exceptions should be cached else FALSE.
     */
    public function __construct(
        CacheAdapterInterface $adapter,
        FormatterInterface $formatter = null,
        $lifetime = null,
        $cacheException = true
    ) {
        $this->setAdapter($adapter);
        $this->setFormatter($formatter ?: new Formatter());
        $this->setlifetime($lifetime);
        $this->cacheException($cacheException);
    }

    /**
     * Gets the adapter.
     *
     * @return \Ivory\HttpAdapter\Event\Cache\Adapter\CacheAdapterInterface The adapter.
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Sets the adapter.
     *
     * @param \Ivory\HttpAdapter\Event\Cache\Adapter\CacheAdapterInterface $adapter The adapter.
     */
    public function setAdapter(CacheAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Gets the formatter.
     *
     * @return \Ivory\HttpAdapter\Event\Formatter\FormatterInterface The formatter.
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * Sets the formatter.
     *
     * @param \Ivory\HttpAdapter\Event\Formatter\FormatterInterface $formatter The formatter.
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * Gets the lifetime.
     *
     * @return integer|null The life time.
     */
    public function getlifetime()
    {
        return $this->lifetime;
    }

    /**
     * Sets the lifetime.
     *
     * @param integer|null $lifetime The life time.
     */
    public function setlifetime($lifetime = null)
    {
        $this->lifetime = $lifetime;
    }

    /**
     * {@inheritdoc}
     */
    public function cacheException($cacheException = null)
    {
        if ($cacheException !== null) {
            $this->cacheException = $cacheException;
        }

        return $this->cacheException;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse(InternalRequestInterface $internalRequest, MessageFactoryInterface $messageFactory)
    {
        if (!$this->adapter->has($id = $this->getIdentifier($internalRequest, 'response'))) {
            return;
        }

        $response = $this->unserializeResponse($this->adapter->get($id), $messageFactory);

        return $response->withParameter('request', $internalRequest);
    }

    /**
     * {@inheritdoc}
     */
    public function getException(InternalRequestInterface $internalRequest, MessageFactoryInterface $messageFactory)
    {
        if (!$this->cacheException || !$this->adapter->has($id = $this->getIdentifier($internalRequest, 'exception'))) {
            return;
        }

        $exception = $this->unserializeException($this->adapter->get($id));
        $exception->setRequest($internalRequest);

        return $exception;
    }

    /**
     * {@inheritdoc}
     */
    public function saveResponse(ResponseInterface $response, InternalRequestInterface $internalRequest)
    {
        if (!$this->adapter->has($id = $this->getIdentifier($internalRequest, 'response'))) {
            $this->adapter->set($id, $this->serializeResponse($response), $this->lifetime);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function saveException(HttpAdapterException $exception, InternalRequestInterface $internalRequest)
    {
        if ($this->cacheException && !$this->adapter->has($id = $this->getIdentifier($internalRequest, 'exception'))) {
            $this->adapter->set($id, $this->serializeException($exception), $this->lifetime);
        }
    }

    /**
     * Gets the adapter identifier.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     * @param string                                              $context         The context.
     *
     * @return string The adapter identifier.
     */
    private function getIdentifier(InternalRequestInterface $internalRequest, $context)
    {
        return sha1($context.$this->serializeInternalRequest($internalRequest));
    }

    /**
     * Serializes an internal request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     *
     * @return string The serialized internal request.
     */
    private function serializeInternalRequest(InternalRequestInterface $internalRequest)
    {
        $formattedInternalRequest = $this->formatter->formatRequest($internalRequest);
        unset($formattedInternalRequest['parameters']);

        return $this->serialize($formattedInternalRequest);
    }

    /**
     * Serializes a response.
     *
     * @param \Ivory\HttpAdapter\Message\ResponseInterface $response The response.
     *
     * @return string The serialized response.
     */
    private function serializeResponse(ResponseInterface $response)
    {
        return $this->serialize($this->formatter->formatResponse($response));
    }

    /**
     * Serializes an exception.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterException $exception The exception.
     *
     * @return string The serialized exception.
     */
    private function serializeException(HttpAdapterException $exception)
    {
        return $this->serialize($this->formatter->formatException($exception));
    }

    /**
     * Unserializes a response.
     *
     * @param string                                             $serialized The cached response.
     * @param \Ivory\HttpAdapter\Message\MessageFactoryInterface $messageFactory The message factory.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    private function unserializeResponse($serialized, MessageFactoryInterface $messageFactory)
    {
        return $this->createResponse($this->unserialize($serialized), $messageFactory);
    }

    /**
     * Unserializes an exception.
     *
     * @param string $serialized The cached exception.
     *s
     * @return \Ivory\HttpAdapter\HttpAdapterException The exception.
     */
    private function unserializeException($serialized)
    {
        return $this->createException($this->unserialize($serialized));
    }

    /**
     * Creates a response.
     *
     * @param array                                              $unserialized   The unserialized response.
     * @param \Ivory\HttpAdapter\Message\MessageFactoryInterface $messageFactory The message factory.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    private function createResponse(array $unserialized, MessageFactoryInterface $messageFactory)
    {
        return $messageFactory->createResponse(
            $unserialized['status_code'],
            $unserialized['protocol_version'],
            $unserialized['headers'],
            $unserialized['body'],
            $unserialized['parameters']
        );
    }

    /**
     * Creates an exception.
     *
     * @param array $unserialized The unserialized exception.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException The exception.
     */
    private function createException(array $unserialized)
    {
        return new HttpAdapterException($unserialized['message']);
    }

    /**
     * Serializes data.
     *
     * @param array $data The data.
     *
     * @return string The serialized data.
     */
    private function serialize(array $data)
    {
        return json_encode($data);
    }

    /**
     * Unserializes data.
     *
     * @param string $data The serialized data.
     *
     * @return array The unserialized data.
     */
    private function unserialize($data)
    {
        return json_decode($data, true);
    }
}
