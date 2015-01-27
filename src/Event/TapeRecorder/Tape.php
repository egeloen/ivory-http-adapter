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
use Symfony\Component\Yaml\Yaml;

/**
 * Tape
 *
 * @author Jérôme Gamez <jerome@gamez.name>
 */
class Tape implements TapeInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var TrackMatcher
     */
    private $trackMatcher;

    /**
     * @var TrackInterface[]
     */
    private $tracks;

    /**
     * @var string
     */
    private $storagePath;

    /**
     * @var Converter
     */
    private $converter;

    /**
     * Initializes a tape with the given name and loads already existing tracks from the file storage.
     *
     * @param string $name        The name of the tap.
     * @param string $storagePath The path where the tape is stored.
     */
    public function __construct($name, $storagePath)
    {
        $this->name = $name;
        $this->storagePath = $storagePath;

        $this->trackMatcher = new TrackMatcher();
        $this->converter = new Converter();
        $this->load();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    public function getStoragePath()
    {
        if (!file_exists($this->storagePath)) {
            mkdir($this->storagePath, 0777, true);
        }

        return $this->storagePath;
    }

    /**
     * {@inheritdoc}
     */
    public function startRecording(RequestInterface $request)
    {
        $track = new Track($request);
        $request->setParameter('track', $track);

        $this->writeTrack($track);
    }

    /**
     * {@inheritdoc}
     */
    public function replay(TrackInterface $track)
    {
        if ($track->hasException()) {
            $this->replayException($track); // throws an HttpAdapterException
        }

        if ($track->hasResponse()) {
            $this->replayResponse($track); // throws a TapeRecorderException
        }
    }

    /**
     * {@inheritdoc}
     */
    public function finishRecording(
        TrackInterface $track,
        ResponseInterface $response = null,
        HttpAdapterException $exception = null
    ) {
        $track->setResponse($response);
        $track->setException($exception);

        $this->writeTrack($track);
    }

    /**
     * {@inheritdoc}
     */
    public function replayException(TrackInterface $track)
    {
        throw $track->getException();
    }

    /**
     * {@inheritdoc}
     */
    public function replayResponse(TrackInterface $track)
    {
        $e = TapeRecorderException::interceptingRequest();
        $e->setResponse($track->getResponse());
        throw $e;
    }

    /**
     * {@inheritdoc}
     */
    public function writeTrack(TrackInterface $track)
    {
        $newTracks = array();

        foreach ($this->tracks as $key => $existing) {
            if (!$this->trackMatcher->matchByRequest($existing, $track->getRequest())) {
                $newTracks[] = $existing;
            }
        }

        $newTracks[] = $track;

        $this->tracks = $newTracks;
    }

    /**
     * {@inheritdoc}
     */
    public function getTracks()
    {
        return $this->tracks;
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        $this->tracks = array();

        $filePath = $this->getFilePath();

        if (is_file($filePath) && is_readable($filePath)) {
            $data = Yaml::parse($filePath);
            foreach ($data as $item) {
                $this->writeTrack($this->converter->arrayToTrack($item));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function store()
    {
        $filePath = $this->getFilePath();
        $data = array();
        foreach ($this->tracks as $track) {
            $data[] = $this->converter->trackToArray($track);
        }

        file_put_contents($filePath, Yaml::dump($data, 4));
    }

    /**
     * {@inheritdoc}
     */
    public function hasTrackForRequest(RequestInterface $request)
    {
        foreach ($this->tracks as $track) {
            if ($this->trackMatcher->matchByRequest($track, $request)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTrackForRequest(RequestInterface $request)
    {
        foreach ($this->tracks as $track) {
            if ($this->trackMatcher->matchByRequest($track, $request)) {
                return $track;
            }
        }

        return new Track($request);
    }

    /**
     * Returns a file path for the current tape.
     *
     * @return string
     */
    private function getFilePath()
    {
        return $this->getStoragePath().DIRECTORY_SEPARATOR.$this->getName().'.yml';
    }
}
