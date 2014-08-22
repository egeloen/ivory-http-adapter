<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Normalizer;

/**
 * Abstract url normalizer.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractUrlNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Gets the valid url provider.
     *
     * @return array The valid url provider.
     */
    public function validUrlProvider()
    {
        return array(
            array('http://egeloen.fr'),
            array('http://egeloen.fr/path'),
        );
    }

    /**
     * Gets the invalid url provider.
     *
     * @return array The invalid url provider.
     */
    public function invalidUrlProvider()
    {
        return array(
            array('egeloen.fr'),
            array('/path'),
        );
    }
}
