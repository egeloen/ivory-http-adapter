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

use Ivory\HttpAdapter\Message\RequestInterface;

/**
 * Track matcher
 *
 * @author Jérôme Gamez <jerome@gamez.name>
 */
class TrackMatcher
{
    /**
     * The formatter.
     *
     * @var Converter
     */
    private $converter;

    /**
     * Initializes the matcher.
     */
    public function __construct()
    {
        $this->converter = new Converter();
    }

    /**
     * Returns TRUE when the given Track's request matches the given request
     *
     * @param TrackInterface           $track   The track.
     * @param RequestInterface $request The request.
     *
     * @return bool TRUE if the Track's request matches the given request, FALSE if not.
     */
    public function matchByRequest(TrackInterface $track, RequestInterface $request)
    {
        $trackRequestData = $this->converter->requestToArray($track->getRequest());
        $requestData = $this->converter->requestToArray($request);

        // If the diff is empty, this means that all keys from the Track request are available in the array
        // from the other request, so they can be considered equal.
        $diff = $this->array_diff_assoc_recursive($requestData, $trackRequestData);
        $isEqual = empty($diff);

        return $isEqual;
    }

    /**
     * Recursively computes the difference between two arrays.
     *
     * This is needed to compare two requests, because an HTTP Adapter implementation can change the request during
     * the execution of the doSendInternalRequest() method.
     *
     * @link http://php.net/manual/en/function.array-diff-assoc.php#111675 Reference implementation
     * @codeCoverageIgnore
     *
     * @param  array $a
     * @param  array $b
     * @return array
     */
    private function array_diff_assoc_recursive(array $a, array $b)
    {
        $difference = array();
        foreach ($a as $key => $value) {
            if (is_array($value)) {
                if (!isset($b[$key]) || !is_array($b[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = $this->array_diff_assoc_recursive($value, $b[$key]);
                    if (!empty($new_diff)) {
                        $difference[$key] = $new_diff;
                    }
                }
            } elseif (!array_key_exists($key, $b) || $b[$key] !== $value) {
                $difference[$key] = $value;
            }
        }

        return $difference;
    }
}
