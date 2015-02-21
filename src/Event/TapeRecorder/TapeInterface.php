<?php

/**
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\TapeRecorder;

use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\RequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * Tape.
 *
 * @author Jérôme Gamez <jerome@gamez.name>
 */
interface TapeInterface
{
    /**
     * Gets the name of the tape.
     *
     * @return string
     */
    public function getName();

    /**
     * Starts recording.
     *
     * @param RequestInterface $request The request.
     *
     * @return void No return value.
     */
    public function startRecording(RequestInterface $request);

    /**
     * Replays a track, if it has either a response or an exception.
     *
     * @param TrackInterface $track The track to replay.
     *
     * @throws HttpAdapterException  When an exception is replayed.
     * @throws TapeRecorderException When a response is replayed.
     *
     * @return void No return value.
     */
    public function replay(TrackInterface $track);

    /**
     * Writes the track to the tape.
     *
     * @param TrackInterface            $track     The track to write.
     * @param ResponseInterface|null    $response  The response to write into the track.
     * @param HttpAdapterException|null $exception The exception to write into the track.
     *
     * @return void No return value.
     */
    public function finishRecording(
        TrackInterface $track,
        ResponseInterface $response = null,
        HttpAdapterException $exception = null
    );

    /**
     * Replays the exception of the given track.
     *
     * @param TrackInterface $track The track.
     *
     * @throws HttpAdapterException The exception to be replayed
     *
     * @return void No return value.
     */
    public function replayException(TrackInterface $track);

    /**
     * Replays the response of the given track.
     *
     * This is done by throwing a TapeRecorderException, which will trigger the exception event of the
     * TapeRecorderSubscriber.
     *
     * @param TrackInterface $track The track.
     *
     * @throws TapeRecorderException The Tape Recorder exception.
     *
     * @return void No return value.
     */
    public function replayResponse(TrackInterface $track);

    /**
     * (Over)Writes a track to the current tape.
     *
     * @param TrackInterface $track
     */
    public function writeTrack(TrackInterface $track);

    /**
     * Checks whether a track exists for the given request.
     *
     * @param RequestInterface $request The request.
     *
     * @return bool TRUE if a track exists, FALSE if not.
     */
    public function hasTrackForRequest(RequestInterface $request);

    /**
     * Returns a track for the given request. If a track does not already exist, creates a new one.
     *
     * @param RequestInterface $request The request.
     *
     * @return TrackInterface The (new) track.
     */
    public function getTrackForRequest(RequestInterface $request);

    /**
     * Returns the tracks.
     *
     * @return TrackInterface[] The tracks.
     */
    public function getTracks();

    /**
     * Loads already existing tracks into the tape.
     *
     * @return void No return value.
     */
    public function load();

    /**
     * Stores the tape.
     *
     * @return void No return value.
     */
    public function store();
}
