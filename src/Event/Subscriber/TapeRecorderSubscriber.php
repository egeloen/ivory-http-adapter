<?php

/**
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\Subscriber;

use Ivory\HttpAdapter\Event\Events;
use Ivory\HttpAdapter\Event\ExceptionEvent;
use Ivory\HttpAdapter\Event\TapeRecorder\Tape;
use Ivory\HttpAdapter\Event\TapeRecorder\TapeRecorderException;
use Ivory\HttpAdapter\Event\PostSendEvent;
use Ivory\HttpAdapter\Event\PreSendEvent;
use Ivory\HttpAdapter\Event\TapeRecorder\TrackInterface;
use Ivory\HttpAdapter\HttpAdapterException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Tape subscriber.
 *
 * @author Jérôme Gamez <jerome@gamez.name>
 */
class TapeRecorderSubscriber implements EventSubscriberInterface
{
    const RECORDING_MODE_ONCE = 1;      // Performs a real request and stores it to a fixture, unless the fixture
    // already exists.
    const RECORDING_MODE_OVERWRITE = 2; // Always performs a real request and overwrites the fixture.
    const RECORDING_MODE_NEVER = 3;     // Always performs a real request and does not write a fixture.

    public static $recordingModes = array(
        self::RECORDING_MODE_ONCE => 'once',
        self::RECORDING_MODE_OVERWRITE => 'overwrite',
        self::RECORDING_MODE_NEVER => 'never'
    );

    /**
     * @var bool
     */
    private $isRecording;

    /**
     * @var string
     */
    private $path;

    /**
     * The current tape
     *
     * @var Tape
     */
    private $currentTape;

    /**
     * The current recording mode.
     *
     * @var int
     */
    private $recordingMode;

    /**
     * Initializes the subscriber.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        // @codeCoverageIgnoreStart
        if (!class_exists('Symfony\Component\Yaml\Yaml')) {
            throw new \RuntimeException('You need the symfony/yaml library to use the Tape Recorder subscriber');
        }
        // @codeCoverageIgnoreEnd

        $this->path = $path;
        $this->isRecording = false;
        $this->recordingMode = self::RECORDING_MODE_ONCE;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::PRE_SEND        => array('onPreSend', 400),
            Events::POST_SEND       => array('onPostSend', 400),
            Events::EXCEPTION       => array('onException', 400),
        );
    }

    /**
     * Sets the recording mode.
     *
     * @param int $recordingMode The recording mode.
     *
     * @return void No return value.
     */
    public function setRecordingMode($recordingMode)
    {
        if (!array_key_exists($recordingMode, self::$recordingModes)) {
            throw new \InvalidArgumentException(sprintf('Undefined recording mode %s.', $recordingMode));
        }

        $this->recordingMode = $recordingMode;
    }

    /**
     * Inserts the tape with the given name.
     *
     * @param $name string The name.
     *
     * @return void No return value.
     */
    public function insertTape($name)
    {
        if (isset($this->currentTape)) {
            throw new \OutOfBoundsException("Another tape is already inserted.");
        }

        $this->currentTape = new Tape($name, $this->path);
    }

    /**
     * Ejects the currently inserted tape.
     *
     * @return void No return value.
     *
     * @codeCoverageIgnore
     */
    public function eject()
    {
        if (!$this->currentTape) {
            // Not throwing an exception because no harm is done.
            return;
        }

        $this->stopRecording();
        $this->currentTape->store();
        unset($this->currentTape);
    }

    /**
     * Starts recording.
     *
     * @return void No return value.
     */
    public function startRecording()
    {
        if (!$this->currentTape) {
            throw new \OutOfBoundsException("No tape has been inserted.");
        }

        if ($this->recordingMode !== self::RECORDING_MODE_NEVER) {
            $this->isRecording = true;
        }
    }

    /**
     * Stops recording.
     *
     * @return void No return value.
     *
     * @codeCoverageIgnore
     */
    public function stopRecording()
    {
        $this->isRecording = false;
    }

    /**
     * On pre send event.
     *
     * @param  PreSendEvent                               $event The pre send event.
     * @throws TapeRecorderException|HttpAdapterException
     */
    public function onPreSend(PreSendEvent $event)
    {
        if (!$this->isRecording) {
            return;
        }

        $request = $event->getRequest();

        if ($this->currentTape->hasTrackForRequest($request)
            && $this->recordingMode !== self::RECORDING_MODE_OVERWRITE
        ) {
            $track = $this->currentTape->getTrackForRequest($request);
            $request->setParameter('track', $track);
            $this->currentTape->replay($track);
        }

        $this->currentTape->startRecording($request);
    }

    /**
     * On post send event.
     *
     * We reach this event when the request has not been intercepted.
     *
     * @param PostSendEvent $event The post send event.
     */
    public function onPostSend(PostSendEvent $event)
    {
        if (!$this->isRecording) {
            return;
        }

        $request = $event->getRequest();

        if (!$request->hasParameter('track')) {
            // Nothing to do here. Since we are in recording mode, the request should
            // have a track, but we check nonetheless, it's better to be safe than sorry
            return;
        }

        /** @var TrackInterface $track */
        $track = $request->getParameter('track');
        $this->currentTape->finishRecording(
            $track,
            $event->getResponse(),
            $event->hasException() ? $event->getException() : null
        );
    }

    /**
     * We arrive here when the request has successfully been intercepted
     *
     * @param ExceptionEvent $event The exception event.
     */
    public function onException(ExceptionEvent $event)
    {
        if (!$this->isRecording) {
            return;
        }

        $exception = $event->getException();
        $request = $exception->getRequest();

        if (!$request->hasParameter('track')) {
            // Nothing to do here. Since we are in recording mode, the request should
            // have a track, but we check nonetheless, it's better to be safe than sorry
            return;
        }

        /** @var TrackInterface $track */
        $track = $request->getParameter('track');

        if (!($exception instanceof TapeRecorderException)) {
            // Normal exception, let's store it in the track for the next time
            $this->currentTape->finishRecording(
                $track,
                $event->getResponse(),
                $event->getException()
            );

            return;
        }

        // We are in replay mode
        if ($track->hasResponse()) {
            $event->setResponse($track->getResponse());
        }

        if ($track->hasException()) {
            $event->setException($track->getException());
        }
    }
}
