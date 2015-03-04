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

/**
 * Internal request interface.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface InternalRequestInterface extends RequestInterface
{
    /**
     * Gets the datas.
     *
     * @return array The datas.
     */
    public function getDatas();

    /**
     * Checks if there is the data.
     *
     * @param string $name The data name.
     *
     * @return boolean TRUE if there is the data else FALSE.
     */
    public function hasData($name);

    /**
     * Gets the data.
     *
     * @param string $name The data name.
     *
     * @return mixed The data value.
     */
    public function getData($name);

    /**
     * Sets the data.
     *
     * @param string $name  The data name.
     * @param mixed  $value The data value.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The new internal request.
     */
    public function withData($name, $value);

    /**
     * Adds the data.
     *
     * @param string $name  The data name.
     * @param mixed  $value The data value.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The new internal request.
     */
    public function withAddedData($name, $value);

    /**
     * Removes a data.
     *
     * @param string $name The data name.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The new internal request.
     */
    public function withoutData($name);

    /**
     * Gets the files.
     *
     * @return array The files.
     */
    public function getFiles();

    /**
     * Checks if there is the file.
     *
     * @param string $name The file name.
     *
     * @return boolean TRUE if there is the file else FALSE.
     */
    public function hasFile($name);

    /**
     * Gets a file.
     *
     * @param string $name The file name.
     *
     * @return string The file.
     */
    public function getFile($name);

    /**
     * Sets a file.
     *
     * @param string $name The file name.
     * @param string $file The file.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The new internal request.
     */
    public function withFile($name, $file);

    /**
     * Adds a file.
     *
     * @param string $name The file name.
     * @param string $file The file.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The new internal request.
     */
    public function withAddedFile($name, $file);

    /**
     * Removes a file.
     *
     * @param string $name The file name.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The new internal request.
     */
    public function withoutFile($name);
}
