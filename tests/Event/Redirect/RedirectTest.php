<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\Redirect;

use Ivory\HttpAdapter\Event\Redirect\Redirect;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\MessageFactoryInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RedirectTest extends AbstractTestCase
{
    /**
     * @var Redirect
     */
    private $redirect;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->redirect = new Redirect();
    }

    public function testDefaultState()
    {
        $this->assertSame(5, $this->redirect->getMax());
        $this->assertFalse($this->redirect->isStrict());
        $this->assertTrue($this->redirect->getThrowException());
    }

    public function testInitialState()
    {
        $this->redirect = new Redirect($max = 10, true, false);

        $this->assertSame($max, $this->redirect->getMax());
        $this->assertTrue($this->redirect->isStrict());
        $this->assertFalse($this->redirect->getThrowException());
    }

    public function testSetMax()
    {
        $this->redirect->setMax($max = 10);

        $this->assertSame($max, $this->redirect->getMax());
    }

    public function testStrict()
    {
        $this->redirect->setStrict(true);

        $this->assertTrue($this->redirect->isStrict());
    }

    public function testSetThrowException()
    {
        $this->redirect->setThrowException(false);

        $this->assertFalse($this->redirect->getThrowException());
    }

    /**
     * @param int $statusCode
     *
     * @dataProvider validStatusCodeProvider
     */
    public function testCreateRedirectRequestWithoutRedirectResponse($statusCode)
    {
        $response = $this->createResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue($statusCode));

        $response
            ->expects($this->any())
            ->method('hasHeader')
            ->with($this->identicalTo('Location'))
            ->will($this->returnValue(true));

        $this->assertFalse($this->redirect->createRedirectRequest(
            $response,
            $this->createRequestMock(),
            $this->createHttpAdapterMock()
        ));
    }

    /**
     * @param int $redirectCount
     * @param int $max
     *
     * @dataProvider maxRedirectReachedProvider
     */
    public function testCreateRedirectRequestWithMaxRedirectReachedReturnFalse($redirectCount, $max)
    {
        $response = $this->createResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(300));

        $response
            ->expects($this->any())
            ->method('hasHeader')
            ->with($this->identicalTo('Location'))
            ->will($this->returnValue(true));

        $request = $this->createRequestMock();
        $request
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo(Redirect::REDIRECT_COUNT))
            ->will($this->returnValue($redirectCount));

        $this->redirect->setThrowException(false);
        $this->redirect->setMax($max);

        $this->assertFalse($this->redirect->createRedirectRequest(
            $response,
            $request,
            $this->createHttpAdapterMock()
        ));
    }

    /**
     * @param int $redirectCount
     * @param int $max
     *
     * @dataProvider maxRedirectReachedProvider
     */
    public function testCreateRedirectRequestWithMaxRedirectReachedThrowException($redirectCount, $max)
    {
        $response = $this->createResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(300));

        $response
            ->expects($this->any())
            ->method('hasHeader')
            ->with($this->identicalTo('Location'))
            ->will($this->returnValue(true));

        $request = $this->createRequestMock();
        $request
            ->expects($this->any())
            ->method('hasParameter')
            ->with($this->identicalTo(Redirect::PARENT_REQUEST))
            ->will($this->returnValue(true));

        $request
            ->expects($this->exactly(2))
            ->method('getParameter')
            ->will($this->returnValueMap([
                [Redirect::REDIRECT_COUNT, $redirectCount],
                [Redirect::PARENT_REQUEST, $rootRequest = $this->createRequestMock()],
            ]));

        $this->redirect->setThrowException(true);
        $this->redirect->setMax($max);

        try {
            $this->redirect->createRedirectRequest($response, $request, $this->createHttpAdapterMock());
            $this->fail();
        } catch (HttpAdapterException $e) {
            $this->assertSame(
                'An error occurred when fetching the URI "http://egeloen.fr" with the adapter "http_adapter" ("Max redirects exceeded (5)").',
                $e->getMessage()
            );

            $this->assertSame($rootRequest, $e->getRequest());
        }
    }

    /**
     * @param int  $statusCode
     * @param bool $strict
     * @param bool $clear
     *
     * @dataProvider redirectStatusCodeProvider
     */
    public function testCreateRedirectRequestWithRedirectResponse($statusCode, $strict = false, $clear = true)
    {
        $response = $this->createResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue($statusCode));

        $response
            ->expects($this->any())
            ->method('hasHeader')
            ->with($this->identicalTo('Location'))
            ->will($this->returnValue(true));

        $response
            ->expects($this->any())
            ->method('getHeaderLine')
            ->with($this->identicalTo('Location'))
            ->will($this->returnValue($location = 'http://egeloen.fr/'));

        $request = $this->createRequestMock();
        $request
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue($method = InternalRequestInterface::METHOD_POST));

        $request
            ->expects($this->any())
            ->method('getProtocolVersion')
            ->will($this->returnValue($protocolVersion = InternalRequestInterface::PROTOCOL_VERSION_1_0));

        $request
            ->expects($this->any())
            ->method('getHeaders')
            ->will($this->returnValue($headers = ['header' => ['foo']]));

        $request
            ->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($body = $this->createMock('Psr\Http\Message\StreamInterface')));

        $request
            ->expects($this->any())
            ->method('getDatas')
            ->will($this->returnValue($datas = ['data' => 'foo']));

        $request
            ->expects($this->any())
            ->method('getFiles')
            ->will($this->returnValue($files = ['file' => 'foo']));

        $request
            ->expects($this->any())
            ->method('getParameters')
            ->will($this->returnValue($parameters = ['parameter' => 'foo']));

        $messageFactory = $this->createMessageFactoryMock();
        $messageFactory
            ->expects($this->once())
            ->method('createInternalRequest')
            ->with(
                $this->identicalTo($location),
                $this->identicalTo($clear ? InternalRequestInterface::METHOD_GET : $method),
                $this->identicalTo($protocolVersion),
                $this->identicalTo($headers),
                $this->identicalTo($clear ? [] : $datas),
                $this->identicalTo($clear ? [] : $files),
                $this->identicalTo($parameters)
            )
            ->will($this->returnValue($redirectRequest = $this->createRequestMock()));

        if ($clear) {
            $redirectRequest
                ->expects($this->exactly(2))
                ->method('withoutHeader')
                ->will($this->returnValueMap([
                    ['Content-Type', $redirectRequest],
                    ['Content-Length', $redirectRequest],
                ]));
        } else {
            $redirectRequest
                ->expects($this->once())
                ->method('withBody')
                ->with($this->identicalTo($body))
                ->will($this->returnValue($redirectRequest));
        }

        $redirectRequest
            ->expects($this->exactly(2))
            ->method('withParameter')
            ->will($this->returnValueMap([
                [Redirect::PARENT_REQUEST, $request, $redirectRequest],
                [Redirect::REDIRECT_COUNT, 1, $redirectRequest],
            ]));

        $this->redirect->setStrict($strict);

        $this->assertSame($redirectRequest, $this->redirect->createRedirectRequest(
            $response,
            $request,
            $this->createHttpAdapterMock($messageFactory)
        ));
    }

    public function testPrepareResponse()
    {
        $request = $this->createRequestMock();
        $request
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo(Redirect::REDIRECT_COUNT))
            ->will($this->returnValue($redirectCount = 1));

        $request
            ->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue($uri = 'http://egeloen.fr/'));

        $response = $this->createResponseMock();
        $response
            ->expects($this->exactly(2))
            ->method('withParameter')
            ->will($this->returnValueMap([
                [Redirect::REDIRECT_COUNT, $redirectCount, $response],
                [Redirect::EFFECTIVE_URI, $uri, $response],
            ]));

        $this->redirect->prepareResponse($response, $request);
    }

    public function testCreateRedirectRequestWillDiscardHostHeaderFromParentRequest()
    {
        $request = $this->createRequestMock();

        $headers = ['X-Foo' => 'Bar'];

        $request
            ->expects($this->any())
            ->method('getHeaders')
            ->will($this->returnValue(array_merge($headers, ['Host' => 'egeloen.fr'])));

        $request
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo(Redirect::REDIRECT_COUNT))
            ->will($this->returnValue(0));

        $request
            ->expects($this->any())
            ->method('getProtocolVersion')
            ->will($this->returnValue($protocolVersion = InternalRequestInterface::PROTOCOL_VERSION_1_0));

        $request
            ->expects($this->any())
            ->method('getParameters')
            ->will($this->returnValue($parameters = []));

        $request
            ->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($body = $this->createMock('Psr\Http\Message\StreamInterface')));

        $response = $this->createResponseMock();

        $response
            ->method('getStatusCode')
            ->will($this->returnValue(301));

        $response
            ->method('getHeaderLine')
            ->with($this->identicalTo('Location'))
            ->will($this->returnValue($location = 'http://google.com'));

        $response
            ->method('hasHeader')
            ->will($this->returnValue(true));

        $messageFactory = $this->createMessageFactoryMock();

        $messageFactory
            ->expects($this->once())
            ->method('createInternalRequest')
            ->with(
                $this->identicalTo($location),
                $this->identicalTo(InternalRequestInterface::METHOD_GET),
                $this->identicalTo($protocolVersion),
                $this->identicalTo($headers),
                $this->identicalTo([]),
                $this->identicalTo([]),
                $this->identicalTo($parameters)
            )
            ->will($this->returnValue($redirectRequest = $this->createRequestMock()));

        $redirectRequest
            ->expects($this->exactly(2))
            ->method('withoutHeader')
            ->will($this->returnValueMap([
                ['Content-Type', $redirectRequest],
                ['Content-Length', $redirectRequest],
            ]));

        $redirectRequest
            ->expects($this->exactly(2))
            ->method('withParameter')
            ->will($this->returnValueMap([
                [Redirect::PARENT_REQUEST, $request, $redirectRequest],
                [Redirect::REDIRECT_COUNT, 1, $redirectRequest],
            ]));

        $this->redirect->setStrict(false);

        $httpAdapter = $this->createHttpAdapterMock($messageFactory);

        $this->assertSame($redirectRequest, $this->redirect->createRedirectRequest($response, $request, $httpAdapter));
    }

    /**
     * @return array
     */
    public function validStatusCodeProvider()
    {
        return [
            [100],
            [200],
            [400],
            [500],
        ];
    }

    /**
     * @return array
     */
    public function redirectStatusCOdeProvider()
    {
        return [
            [300],
            [301],
            [302],
            [303],
            [304, false, false],
            [305, false, false],
            [300, true, false],
            [301, true, false],
            [302, true, false],
            [303, true],
            [304, false, false],
            [305, false, false],
        ];
    }

    /**
     * @return array
     */
    public function maxRedirectReachedProvider()
    {
        return [
            [5, 5],
            [6, 5],
            [10, 5],
        ];
    }

    /**
     * @param InternalRequestInterface|null $parent
     *
     * @return InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createRequestMock($parent = null)
    {
        $request = $this->createMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
        $request
            ->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue('http://egeloen.fr'));

        if ($parent !== null) {
            $request
                ->expects($this->any())
                ->method('hasParameter')
                ->with($this->identicalTo(Redirect::PARENT_REQUEST))
                ->will($this->returnValue(true));

            $request
                ->expects($this->any())
                ->method('getParameter')
                ->with($this->identicalTo(Redirect::PARENT_REQUEST))
                ->will($this->returnValue($parent));
        }

        return $request;
    }

    /**
     * @return ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createResponseMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }

    /**
     * @param MessageFactoryInterface|null $messageFactory
     *
     * @return HttpAdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createHttpAdapterMock(MessageFactoryInterface $messageFactory = null)
    {
        $httpAdapter = $this->createMock('Ivory\HttpAdapter\HttpAdapterInterface');
        $httpAdapter
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('http_adapter'));

        $httpAdapter
            ->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue($configuration = $this->createMock('Ivory\HttpAdapter\ConfigurationInterface')));

        $configuration
            ->expects($this->any())
            ->method('getMessageFactory')
            ->will($this->returnValue($messageFactory ?: $this->createMessageFactoryMock()));

        return $httpAdapter;
    }

    /**
     * @return MessageFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createMessageFactoryMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\MessageFactoryInterface');
    }
}
