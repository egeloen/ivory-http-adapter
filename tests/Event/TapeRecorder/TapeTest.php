<?php

/**
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\TapeRecorder;

use Ivory\HttpAdapter\CurlHttpAdapter;
use Ivory\HttpAdapter\Event\TapeRecorder\Tape;
use Ivory\HttpAdapter\Event\TapeRecorder\TapeRecorderException;
use Ivory\HttpAdapter\Event\TapeRecorder\Track;
use Ivory\HttpAdapter\Event\TapeRecorder\TrackInterface;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\MessageFactory;
use Ivory\HttpAdapter\Message\RequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Ivory\HttpAdapter\Message\Stream\StringStream;

/**
 * Tape test.
 *
 * @group TapeRecorderSubscriber
 *
 * @author Jérôme Gamez <jerome@gamez.name>
 */
class TapeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $storagePath;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->storagePath = __DIR__.'/fixtures';
        $this->messageFactory = new MessageFactory();
    }

    public function testStorageDirGetsCreated()
    {
        new Tape('foo', $path = sys_get_temp_dir().'/'.uniqid());
        $this->assertFileExists($path);
        @unlink($path);
    }

    public function testStoreEmptyTape()
    {
        $tape = new Tape($name = 'foo', $path = sys_get_temp_dir().'/'.uniqid());
        $tape->store();
        $this->assertFileExists($file = sprintf('%s/%s.yml', $path, $name));
        @unlink($file);
    }

    public function testStoreTape()
    {
        $tape = new Tape($name = 'foo', $path = sys_get_temp_dir().'/'.uniqid());

        $tape->writeTrack($track = $this->createTrack('http://foo.bar'));
        $tape->store();
        $this->assertFileExists($file = sprintf('%s/%s.yml', $path, $name));

        // Reload
        $tape->load();
        $this->assertCount(1, $tracks = $tape->getTracks());
        $this->assertEquals($track, $tracks[0]);

        @unlink($file);
    }

    public function testLoadExistingTape()
    {
        // $this->prepareFixtureFile(__FUNCTION__); // Only to be used when changing the TapeTest, uncomment before committing

        $tape = $this->createTape(__FUNCTION__);
        $this->assertCount(1, $tracks = $tape->getTracks());
        $track = $tracks[0];
        $this->assertInstanceOf('Ivory\HttpAdapter\Event\TapeRecorder\Track', $track);

        $this->assertInstanceOf('Ivory\HttpAdapter\Message\RequestInterface', $track->getRequest());

        $this->assertTrue($track->hasResponse());
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\ResponseInterface', $track->getResponse());

        $this->assertTrue($track->hasException());
        $this->assertInstanceOf('Ivory\HttpAdapter\HttpAdapterException', $track->getException());
    }

    public function testEmptyTape()
    {
        $tape = $this->createTape(__FUNCTION__);
        $this->assertCount(0, $tape->getTracks());
    }

    public function testStartRecording()
    {
        $tape = $this->createTape(__FUNCTION__);
        $tape->startRecording($request = $this->createRequest());

        $this->assertTrue($request->hasParameter('track'));
        $this->assertInstanceOf('Ivory\HttpAdapter\Event\TapeRecorder\TrackInterface', $request->getParameter('track'));
    }

    public function testFinishRecording()
    {
        $tape = $this->createTape(__FUNCTION__);
        $tape->startRecording($request = $this->createRequest());
        $track = $request->getParameter('track');

        $tape->finishRecording($track, $response = $this->createResponse(), $exception = $this->createException());

        $this->assertTrue($track->hasResponse());
        $this->assertSame($response, $track->getResponse());

        $this->assertTrue($track->hasException());
        $this->assertSame($exception, $track->getException());
    }

    public function testGetTrackForRequestWithEmptyTape()
    {
        $tape = $this->createTape(__FUNCTION__);
        $this->assertFalse($tape->hasTrackForRequest($request = $this->createRequest()));
        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\TapeRecorder\TrackInterface',
            $tape->getTrackForRequest($request)
        );
    }

    public function testGetTrackForRequest()
    {
        $tape = $this->createTape(__FUNCTION__);
        $track = $this->createTrackMock($request = $this->createRequest());
        $tape->writeTrack($track);

        $this->assertTrue($tape->hasTrackForRequest($request));
        $this->assertSame($track, $tape->getTrackForRequest($request));
    }

    public function testWriteTrack()
    {
        $tape = $this->createTape(__FUNCTION__);
        $track1 = $this->createTrackMock($this->createRequest());
        $track2 = $this->createTrackMock($this->createRequest('http://foo.bar'));

        $tape->writeTrack($track1);
        $tape->writeTrack($track2);
        $this->assertCount(2, $tracks = $tape->getTracks());
    }

    public function testWriteTrackTwiceResultsInOneTrackOnly()
    {
        $tape = $this->createTape(__FUNCTION__);
        $track = $this->createTrackMock($this->createRequest());

        $tape->writeTrack($track);
        $tape->writeTrack($track);
        $this->assertCount(1, $tracks = $tape->getTracks());
        $this->assertSame($track, $tracks[0]);
    }

    public function testReplayResponseFromExistingTrack()
    {
        $tape = $this->createTape(__FUNCTION__);
        $track = $this->createTrackMock($request = $this->createRequest(), $response = $this->createResponse());

        try {
            $tape->replay($track);
            $this->fail('TapeRecorderException excepted, none thrown');
        } catch (TapeRecorderException $e) {
            $this->assertSame($response, $e->getResponse());
        }
    }

    public function testReplayExceptionFromExistingTrack()
    {
        $tape = $this->createTape(__FUNCTION__);

        $track = $this->createTrackMock(
            $request = $this->createRequest(),
            $response = $this->createResponse(),
            $exception = $this->createException()
        );

        try {
            $tape->replay($track);
            $this->fail('HttpAdapterException excepted, none thrown');
        } catch (HttpAdapterException $e) {
            $this->assertSame($exception, $e);
        }
    }

    protected function createTape($name)
    {
        return new Tape($name, $this->storagePath);
    }

    /**
     * @param  RequestInterface                                $request
     * @param  ResponseInterface                                       $response
     * @param  HttpAdapterException                                    $exception
     * @return TrackInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createTrackMock(
        RequestInterface $request = null,
        ResponseInterface $response = null,
        HttpAdapterException $exception = null
    ) {
        $request = $request ?: $this->createRequest();

        $track = $this
            ->getMockBuilder('Ivory\HttpAdapter\Event\TapeRecorder\TrackInterface')
            ->getMock();

        $track->expects($this->any())
            ->method('hasRequest')
            ->willReturn(true);

        $track->expects($this->any())
            ->method('getRequest')
            ->willReturn($request);

        $track->expects($this->any())
            ->method('getResponse')
            ->willReturn($response);

        $track->expects($this->any())
            ->method('hasResponse')
            ->willReturn($response ? true : false);

        $track->expects($this->any())
            ->method('getException')
            ->willReturn($exception);

        $track->expects($this->any())
            ->method('hasException')
            ->willReturn($exception ? true : false);

        return $track;
    }

    /**
     * Creates a track for a request with the given url.
     *
     * @param  string $requestUrl
     * @param  bool   $hasResponse
     * @param  bool   $hasException
     * @return Track
     */
    protected function createTrack($requestUrl, $hasResponse = true, $hasException = false)
    {
        $track = new Track($request = $this->createRequest($requestUrl));

        if ($hasResponse) {
            $track->setResponse($this->createResponse());
        }

        if ($hasException) {
            $track->setException($this->createException());
        }

        return $track;
    }

    /**
     * Creates a request.
     *
     * @param  null                     $url
     * @param  TrackInterface           $track
     * @return RequestInterface
     */
    protected function createRequest($url = null, TrackInterface $track = null)
    {
        $request = $this->messageFactory->createRequest($url ?: 'http://egeloen.fr/');

        if ($track) {
            $request->setParameter('track', $track);
        }

        return $request;
    }

    /**
     * Creates a response.
     *
     * @return ResponseInterface
     */
    protected function createResponse()
    {
        return $this->messageFactory->createResponse(200, 'OK', '1.1', array(), new StringStream(null, 3), array());
    }

    protected function createException()
    {
        return new HttpAdapterException('Some message');
    }

    private function prepareFixtureFile($methodName)
    {
        $tape = $this->createTape($methodName);
        $httpAdapter = new CurlHttpAdapter();
        $request = $httpAdapter->getConfiguration()->getMessageFactory()->createRequest('http://httpstat.us/200');
        $response = $httpAdapter->sendRequest($request);
        $exception = new HttpAdapterException();
        $exception->setRequest(
            $httpAdapter->getConfiguration()->getMessageFactory()->createInternalRequest('http://httpstat.us/200')
        );
        $exception->setResponse($response);

        $track = new Track($request);
        $track->setResponse($response);
        $track->setException($exception);

        $tape->writeTrack($track);
        $tape->store();
    }
}
