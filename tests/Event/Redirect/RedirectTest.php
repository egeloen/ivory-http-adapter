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
use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * Redirect test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class RedirectTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\Event\Redirect\Redirect */
    private $redirect;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->redirect = new Redirect();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->redirect);
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
     * @dataProvider redirectResponseProvider
     */
    public function testIsRedirectResponse($expected, $statusCode = 200, $location = false)
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
            ->will($this->returnValue($location));

        $this->assertSame($expected, $this->redirect->isRedirectResponse($response));
    }

    /**
     * @dataProvider maxRedirectRequestProvider
     */
    public function testIsMaxRedirectRequest($expected, $redirectCount, $max)
    {
        $request = $this->createRequestMock();
        $request
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo(Redirect::REDIRECT_COUNT))
            ->will($this->returnValue($redirectCount));

        $this->redirect->setMax($max);

        $this->assertSame($expected, $this->redirect->isMaxRedirectRequest($request));
    }

    /**
     * @dataProvider createRedirectRequestProvider
     */
    public function testCreateRedirectRequest($statusCode, $strict = false, $clear = true)
    {
        $messageFactory = $this->createMessageFactoryMock();
        $messageFactory
            ->expects($this->once())
            ->method('cloneInternalRequest')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue($requestClone = $this->createRequestMock()));

        $response = $this->createResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue($statusCode));

        $response
            ->expects($this->any())
            ->method('getHeader')
            ->with($this->identicalTo('Location'))
            ->will($this->returnValue($location = 'http://egeloen.fr'));

        $requestClone
            ->expects($clear ? $this->once() : $this->never())
            ->method('setMethod')
            ->with($this->identicalTo(InternalRequestInterface::METHOD_GET));

        $requestClone
            ->expects($clear ? $this->once() : $this->never())
            ->method('removeHeaders')
            ->with($this->identicalTo(array('Content-Type', 'Content-Length')));

        $requestClone
            ->expects($clear ? $this->once() : $this->never())
            ->method('clearRawDatas');

        $requestClone
            ->expects($clear ? $this->once() : $this->never())
            ->method('clearDatas');

        $requestClone
            ->expects($clear ? $this->once() : $this->never())
            ->method('clearFiles');

        $requestClone
            ->expects($this->once())
            ->method('setUrl')
            ->with($this->identicalTo($location));

        $requestClone
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                array($this->identicalTo(Redirect::PARENT_REQUEST), $this->identicalTo($request)),
                array($this->identicalTo(Redirect::REDIRECT_COUNT), $this->identicalTo(1))
            );

        $this->redirect->setStrict($strict);

        $this->assertSame($requestClone, $this->redirect->createRedirectRequest($response, $request, $messageFactory));
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
            ->method('getUrl')
            ->will($this->returnValue($url = 'http://egeloen.fr'));

        $response = $this->createResponseMock();
        $response
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                array($this->identicalTo(Redirect::REDIRECT_COUNT), $this->identicalTo($redirectCount)),
                array($this->identicalTo(Redirect::EFFECTIVE_URL), $this->identicalTo($url))
            );

        $this->redirect->prepareResponse($response, $request);
    }

    public function testGetRootRequest()
    {
        $rootRequest = $this->createRequestMock();
        $nodeRequest = $this->createRequestMock($rootRequest);
        $request = $this->createRequestMock($nodeRequest);

        $this->assertSame($rootRequest, $this->redirect->getRootRequest($request));
    }

    /**
     * Gets the redirect response provider.
     *
     * @return array The redirect response provider.
     */
    public function redirectResponseProvider()
    {
        return array(
            array(false),
            array(false, 300),
            array(false, 301),
            array(false, 302),
            array(false, 303),
            array(true, 300, true),
            array(true, 301, true),
            array(true, 302, true),
            array(true, 303, true),
        );
    }

    /**
     * Gets the max redirect request provider.
     *
     * @return array The max redirect request provider.
     */
    public function maxRedirectRequestProvider()
    {
        return array(
            array(false, 0, 5),
            array(false, 4, 5),
            array(true, 5, 5),
            array(true, 6, 5),
            array(true, 10, 5),
        );
    }

    /**
     * Gets the create redirect request provider.
     *
     * @return array The create redirect request provider.
     */
    public function createRedirectRequestProvider()
    {
        return array(
            array(300),
            array(301),
            array(302),
            array(303),
            array(304, false, false),
            array(305, false, false),
            array(300, true, false),
            array(301, true, false),
            array(302, true, false),
            array(303, true),
            array(304, false, false),
            array(305, false, false),
        );
    }

    /**
     * Creates a request mock.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface|null $parent The parent request.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject The request mock.
     */
    private function createRequestMock($parent = null)
    {
        $request = $this->getMock('Ivory\HttpAdapter\Message\InternalRequestInterface');

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
     * Creates a response mock.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject The response mock.
     */
    private function createResponseMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }

    /**
     * Creates a message factory mock.
     *
     * @return \Ivory\HttpAdapter\Message\MessageFactoryInterface|\PHPUnit_Framework_MockObject_MockObject The message factory mock.
     */
    private function createMessageFactoryMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\MessageFactoryInterface');
    }
}
