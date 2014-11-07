<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\Cookie\Jar;

use Ivory\HttpAdapter\Event\Cookie\CookieFactoryInterface;
use Ivory\HttpAdapter\HttpAdapterException;

/**
 * File cookie jar.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class FileCookieJar extends AbstractPersistentCookieJar
{
    /** @var string */
    private $file;

    /**
     * Creates a file cookie jar.
     *
     * @param string                                                      $file          The file.
     * @param \Ivory\HttpAdapter\Event\Cookie\CookieFactoryInterface|null $cookieFactory The cookie factory.
     */
    public function __construct($file, CookieFactoryInterface $cookieFactory = null)
    {
        $this->setFile($file);

        parent::__construct($cookieFactory, file_exists($file));
    }

    /**
     * Gets the file.
     *
     * @return string The file.
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Sets the file.
     *
     * @param string $file The file.
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        if (($data = @file_get_contents($this->file)) === false) {
            $error = error_get_last();
            throw HttpAdapterException::cannotLoadCookieJar($error['message']);
        }

        $this->unserialize($data);
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        if (@file_put_contents($this->file, $this->serialize()) === false) {
            $error = error_get_last();
            throw HttpAdapterException::cannotSaveCookieJar($error['message']);
        }
    }
}
