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

use Ivory\HttpAdapter\Event\Cache\Adapter\CacheAdapterInterface;
use Ivory\HttpAdapter\Event\Formatter\Formatter;
use Ivory\HttpAdapter\Event\Formatter\FormatterInterface;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\MessageFactoryInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Cache implements CacheInterface
{
    /**
     * @var CacheAdapterInterface
     */
    private $adapter;

    /**
     * @var FormatterInterface
     */
    private $formatter;

    /**
     * @var int|null
     */
    private $lifetime;

    /**
     * @var bool
     */
    private $cacheException;

    /**
     * @param CacheAdapterInterface   $adapter
     * @param FormatterInterface|null $formatter
     * @param int|null                $lifetime
     * @param bool                    $cacheException
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
     * @return CacheAdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param CacheAdapterInterface $adapter
     */
    public function setAdapter(CacheAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @return FormatterInterface
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * @param FormatterInterface $formatter
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * @return int|null
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * @param int|null $lifetime
     */
    public function setLifetime($lifetime = null)
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
     * @param InternalRequestInterface $internalRequest
     * @param string                   $context
     *
     * @return string
     */
    private function getIdentifier(InternalRequestInterface $internalRequest, $context)
    {
        return sha1($context.$this->serializeInternalRequest($internalRequest));
    }

    /**
     * @param InternalRequestInterface $internalRequest
     *
     * @return string
     */
    private function serializeInternalRequest(InternalRequestInterface $internalRequest)
    {
        $formattedInternalRequest = $this->formatter->formatRequest($internalRequest);
        unset($formattedInternalRequest['parameters']);

        return $this->serialize($formattedInternalRequest);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return string
     */
    private function serializeResponse(ResponseInterface $response)
    {
        return $this->serialize($this->formatter->formatResponse($response));
    }

    /**
     * @param HttpAdapterException $exception
     *
     * @return string
     */
    private function serializeException(HttpAdapterException $exception)
    {
        return $this->serialize($this->formatter->formatException($exception));
    }

    /**
     * @param string                  $serialized
     * @param MessageFactoryInterface $messageFactory
     *
     * @return ResponseInterface
     */
    private function unserializeResponse($serialized, MessageFactoryInterface $messageFactory)
    {
        return $this->createResponse($this->unserialize($serialized), $messageFactory);
    }

    /**
     * @param string $serialized
     *
     * @return HttpAdapterException
     */
    private function unserializeException($serialized)
    {
        return $this->createException($this->unserialize($serialized));
    }

    /**
     * @param array                   $unserialized
     * @param MessageFactoryInterface $messageFactory
     *
     * @return ResponseInterface
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
     * @param array $unserialized
     *
     * @return HttpAdapterException
     */
    private function createException(array $unserialized)
    {
        return new HttpAdapterException($unserialized['message']);
    }

    /**
     * @param array $data
     *
     * @return string
     */
    private function serialize(array $data)
    {
        return json_encode($data);
    }

    /**
     * @param string $data
     *
     * @return array
     */
    private function unserialize($data)
    {
        return json_decode($data, true);
    }
}
