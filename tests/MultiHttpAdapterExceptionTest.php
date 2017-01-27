<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter;

use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Ivory\HttpAdapter\MultiHttpAdapterException;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class MultiHttpAdapterExceptionTest extends AbstractTestCase
{
    /**
     * @var MultiHttpAdapterException
     */
    private $multiHttpAdapterException;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->multiHttpAdapterException = new MultiHttpAdapterException();
    }

    public function testDefaultState()
    {
        $this->assertSame(
            'An error occurred when sending multiple requests.',
            $this->multiHttpAdapterException->getMessage()
        );

        $this->assertNoExceptions();
        $this->assertNoResponses();
    }

    public function testInitialState()
    {
        $this->multiHttpAdapterException = new MultiHttpAdapterException(
            $exceptions = [$this->createExceptionMock()],
            $responses = [$this->createResponseMock()]
        );

        $this->assertExceptions($exceptions);
        $this->assertResponses($responses);
    }

    public function testSetExceptions()
    {
        $this->multiHttpAdapterException->setExceptions($exceptions = [$this->createExceptionMock()]);

        $this->assertExceptions($exceptions);
    }

    public function testAddExceptions()
    {
        $this->multiHttpAdapterException->setExceptions($exceptions = [$this->createExceptionMock()]);
        $this->multiHttpAdapterException->addExceptions($newExceptions = [$this->createExceptionMock()]);

        $this->assertExceptions(array_merge($exceptions, $newExceptions));
    }

    public function testRemoveExceptions()
    {
        $this->multiHttpAdapterException->setExceptions($exceptions = [$this->createExceptionMock()]);
        $this->multiHttpAdapterException->removeExceptions($exceptions);

        $this->assertNoExceptions();
    }

    public function testClearExceptions()
    {
        $this->multiHttpAdapterException->setExceptions([$this->createExceptionMock()]);
        $this->multiHttpAdapterException->clearExceptions();

        $this->assertNoExceptions();
    }

    public function testAddException()
    {
        $this->multiHttpAdapterException->addException($exception = $this->createExceptionMock());

        $this->assertException($exception);
    }

    public function testRemoveException()
    {
        $this->multiHttpAdapterException->addException($exception = $this->createExceptionMock());
        $this->multiHttpAdapterException->removeException($exception);

        $this->assertNoException($exception);
    }

    public function testSetResponses()
    {
        $this->multiHttpAdapterException->setResponses($responses = [$this->createResponseMock()]);

        $this->assertResponses($responses);
    }

    public function testAddResponses()
    {
        $this->multiHttpAdapterException->setResponses($responses = [$this->createResponseMock()]);
        $this->multiHttpAdapterException->addResponses($newResponses = [$this->createResponseMock()]);

        $this->assertResponses(array_merge($responses, $newResponses));
    }

    public function testRemoveResponses()
    {
        $this->multiHttpAdapterException->setResponses($responses = [$this->createResponseMock()]);
        $this->multiHttpAdapterException->removeResponses($responses);

        $this->assertNoResponses();
    }

    public function testClearResponses()
    {
        $this->multiHttpAdapterException->setResponses([$this->createResponseMock()]);
        $this->multiHttpAdapterException->clearResponses();

        $this->assertNoResponses();
    }

    public function testAddResponse()
    {
        $this->multiHttpAdapterException->addResponse($response = $this->createResponseMock());

        $this->assertResponse($response);
    }

    public function testRemoveResponse()
    {
        $this->multiHttpAdapterException->addResponse($response = $this->createResponseMock());
        $this->multiHttpAdapterException->removeResponse($response);

        $this->assertNoResponse($response);
    }

    /**
     * @return HttpAdapterException|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createExceptionMock()
    {
        return $this->createMock('Ivory\HttpAdapter\HttpAdapterException');
    }

    /**
     * @return ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createResponseMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }

    /**
     * @param array $exceptions
     */
    private function assertExceptions(array $exceptions)
    {
        $this->assertTrue($this->multiHttpAdapterException->hasExceptions());
        $this->assertSame($exceptions, $this->multiHttpAdapterException->getExceptions());

        foreach ($exceptions as $exception) {
            $this->assertException($exception);
        }
    }

    private function assertNoExceptions()
    {
        $this->assertFalse($this->multiHttpAdapterException->hasExceptions());
        $this->assertEmpty($this->multiHttpAdapterException->getExceptions());
    }

    /**
     * @param HttpAdapterException $exception
     */
    private function assertException($exception)
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\HttpAdapterException', $exception);
        $this->assertTrue($this->multiHttpAdapterException->hasException($exception));
    }

    /**
     * @param string $exception
     */
    private function assertNoException($exception)
    {
        $this->assertFalse($this->multiHttpAdapterException->hasException($exception));
    }

    /**
     * @param array $responses
     */
    private function assertResponses(array $responses)
    {
        $this->assertTrue($this->multiHttpAdapterException->hasResponses());
        $this->assertSame($responses, $this->multiHttpAdapterException->getResponses());

        foreach ($responses as $response) {
            $this->assertResponse($response);
        }
    }

    private function assertNoResponses()
    {
        $this->assertFalse($this->multiHttpAdapterException->hasResponses());
        $this->assertEmpty($this->multiHttpAdapterException->getResponses());
    }

    /**
     * @param ResponseInterface $response
     */
    private function assertResponse($response)
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\ResponseInterface', $response);
        $this->assertTrue($this->multiHttpAdapterException->hasResponse($response));
    }

    /**
     * @param string $response
     */
    private function assertNoResponse($response)
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\ResponseInterface', $response);
        $this->assertFalse($this->multiHttpAdapterException->hasResponse($response));
    }
}
