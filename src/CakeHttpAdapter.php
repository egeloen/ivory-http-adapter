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

use Cake\Network\Http\Client;
use Cake\Network\Http\Request;
use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * Cake http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CakeHttpAdapter extends AbstractHttpAdapter
{
    /** @var \Cake\Network\Http\Client */
    private $client;

    /**
     * Creates a Cake http adapter.
     *
     * @param \Cake\Network\Http\Client|null                 $client        The Cake client.
     * @param \Ivory\HttpAdapter\ConfigurationInterface|null $configuration The configuration.
     */
    public function __construct(Client $client = null, ConfigurationInterface $configuration = null)
    {
        parent::__construct($configuration);

        $this->client = $client ?: new Client();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'cake';
    }

    /**
     * {@inheritdoc}
     */
    protected function sendInternalRequest(InternalRequestInterface $internalRequest)
    {
        $request = new Request();

        foreach ($this->prepareHeaders($internalRequest) as $name => $value) {
            $request->header($name, $value);
        }

        $request->method($internalRequest->getMethod());
        $request->body($this->prepareBody($internalRequest));
        $request->url($uri = (string) $internalRequest->getUri());
        $request->version($this->getConfiguration()->getProtocolVersion());

        try {
            $response = $this->client->send($request, array(
                'timeout'  => $this->getConfiguration()->getTimeout(),
                'redirect' => false,
            ));
        } catch (\Exception $e) {
            throw HttpAdapterException::cannotFetchUri($uri, $this->getName(), $e->getMessage());
        }

        return $this->getConfiguration()->getMessageFactory()->createResponse(
            (integer) $response->statusCode(),
            $response->version(),
            $response->headers(),
            $response->body()
        );
    }
}
