<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Message;

use Psr\Http\Message\MessageInterface as PsrMessageInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface MessageInterface extends PsrMessageInterface
{
    const PROTOCOL_VERSION_1_0 = '1.0';
    const PROTOCOL_VERSION_1_1 = '1.1';

    /**
     * @return array
     */
    public function getParameters();

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasParameter($name);

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getParameter($name);

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return MessageInterface
     */
    public function withParameter($name, $value);

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return MessageInterface
     */
    public function withAddedParameter($name, $value);

    /**
     * @param string $name
     *
     * @return MessageInterface
     */
    public function withoutParameter($name);
}
