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

use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * Multi http adapter exception.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MultiHttpAdapterException extends \Exception
{
    /** @var array */
    private $exceptions;

    /** @var array */
    private $responses;

    /**
     * Creates a multi http adapter exception.
     *
     * @param array $exceptions The exceptions.
     * @param array $responses  The responses.
     */
    public function __construct(array $exceptions = array(), array $responses = array())
    {
        parent::__construct('An error occurred when sending multiple requests.');

        $this->setExceptions($exceptions);
        $this->setResponses($responses);
    }

    /**
     * Clears the exceptions.
     */
    public function clearExceptions()
    {
        $this->exceptions = array();
    }

    /**
     * Checks if there are exceptions.
     *
     * @return boolean TRUE if there are exceptions else FALSE.
     */
    public function hasExceptions()
    {
        return !empty($this->exceptions);
    }

    /**
     * Gets the exceptions.
     *
     * @return array The exceptions.
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }

    /**
     * Sets the exceptions.
     *
     * @param array $exceptions The exceptions.
     */
    public function setExceptions(array $exceptions)
    {
        $this->clearExceptions();
        $this->addExceptions($exceptions);
    }

    /**
     * Adds the exceptions.
     *
     * @param array $exceptions The exceptions.
     */
    public function addExceptions(array $exceptions)
    {
        foreach ($exceptions as $exception) {
            $this->addException($exception);
        }
    }

    /**
     * Removes the exceptions.
     *
     * @param array $exceptions The exceptions.
     */
    public function removeExceptions(array $exceptions)
    {
        foreach ($exceptions as $exception) {
            $this->removeException($exception);
        }
    }

    /**
     * Checks if there is an exception.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterException $exception The exception.
     *
     * @return boolean TRUE if there is the exception else FALSE.
     */
    public function hasException(HttpAdapterException $exception)
    {
        return array_search($exception, $this->exceptions, true) !== false;
    }

    /**
     * Adds an exception.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterException $exception The exception.
     */
    public function addException(HttpAdapterException $exception)
    {
        $this->exceptions[] = $exception;
    }

    /**
     * Removes an exception.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterException $exception The exception.
     */
    public function removeException(HttpAdapterException $exception)
    {
        unset($this->exceptions[array_search($exception, $this->exceptions, true)]);
        $this->exceptions = array_values($this->exceptions);
    }

    /**
     * Clears the responses.
     */
    public function clearResponses()
    {
        $this->responses = array();
    }

    /**
     * Checks if there are exceptions.
     *
     * @return boolean TRUE if there are exceptions else FALSE.
     */
    public function hasResponses()
    {
        return !empty($this->responses);
    }

    /**
     * Gets the responses.
     *
     * @return array The responses.
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * Sets the responses.
     *
     * @param array $responses The responses.
     */
    public function setResponses(array $responses)
    {
        $this->clearResponses();
        $this->addResponses($responses);
    }

    /**
     * Adds the responses.
     *
     * @param array $responses The responses.
     */
    public function addResponses(array $responses)
    {
        foreach ($responses as $response) {
            $this->addResponse($response);
        }
    }

    /**
     * Removes the responses.
     *
     * @param array $responses The responses.
     */
    public function removeResponses(array $responses)
    {
        foreach ($responses as $response) {
            $this->removeResponse($response);
        }
    }

    /**
     * Checks if there is a response.
     *
     * @param \Ivory\HttpAdapter\Message\ResponseInterface $response The response.
     *
     * @return boolean TRUE if there is the response else FALSE.
     */
    public function hasResponse(ResponseInterface $response)
    {
        return array_search($response, $this->responses, true) !== false;
    }

    /**
     * Adds a response.
     *
     * @param \Ivory\HttpAdapter\Message\ResponseInterface $response The response.
     */
    public function addResponse(ResponseInterface $response)
    {
        $this->responses[] = $response;
    }

    /**
     * Removes a response.
     *
     * @param \Ivory\HttpAdapter\Message\ResponseInterface $response The response.
     */
    public function removeResponse(ResponseInterface $response)
    {
        unset($this->responses[array_search($response, $this->responses, true)]);
        $this->responses = array_values($this->responses);
    }
}
