<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter;

use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * Requests http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class RequestsHttpAdapter extends AbstractHttpAdapter
{
    /** @var \Requests_Transport */
    private $transport;

    /**
     * Creates a requests http adapter.
     *
     * @param \Requests_Transport|null                       $transport     The transport.
     * @param \Ivory\HttpAdapter\ConfigurationInterface|null $configuration The configuration
     */
    public function __construct(\Requests_Transport $transport = null, ConfigurationInterface $configuration = null)
    {
        parent::__construct($configuration);

        $this->transport = $transport;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'requests';
    }

    /**
     * {@inheritdoc}
     */
    protected function sendInternalRequest(InternalRequestInterface $internalRequest)
    {
        $options = array(
            'timeout'          => $this->getConfiguration()->getTimeout(),
            'connect_timeout'  => $this->getConfiguration()->getTimeout(),
            'protocol_version' => (float) $this->getConfiguration()->getProtocolVersion(),
            'follow_redirects' => 0,
            'data_format'      => 'body',
        );

        if ($this->transport !== null) {
            $options['transport'] = $this->transport;
        }

        try {
            $response = \Requests::request(
                $uri = (string) $internalRequest->getUri(),
                $this->prepareHeaders($internalRequest),
                $this->prepareBody($internalRequest),
                $internalRequest->getMethod(),
                $options
            );
        } catch (\Exception $e) {
            throw HttpAdapterException::cannotFetchUri($uri, $this->getName(), $e->getMessage());
        }

        return $this->getConfiguration()->getMessageFactory()->createResponse(
            $response->status_code,
            sprintf('%.1f', $response->protocol_version),
            $response->headers->getAll(),
            $response->body
        );
    }
}
