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
     * Checks if there are datas.
     *
     * @return boolean TRUE if there are datas else FALSE.
     */
    public function hasDatas();

    /**
     * Checks if there are string datas.
     *
     * @return boolean TRUE if there are string datas else FALSE.
     */
    public function hasStringDatas();

    /**
     * Checks if there are array datas.
     *
     * @return boolean TRUE if there are array datas else FALSE.
     */
    public function hasArrayDatas();

    /**
     * Gets the datas.
     *
     * @return array|string The datas.
     */
    public function getDatas();

    /**
     * Sets the datas.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the datas are a string and there are files.
     *
     * @param array|string $datas The datas.
     */
    public function setDatas($datas);

    /**
     * Checks if there are files.
     *
     * @return boolean TRUE if there are files else FALSE.
     */
    public function hasFiles();

    /**
     * Gets the files.
     *
     * @return array The files.
     */
    public function getFiles();

    /**
     * Sets the files.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If there is data as string and there are files.
     *
     * @param array $files The files.
     */
    public function setFiles(array $files);
}
