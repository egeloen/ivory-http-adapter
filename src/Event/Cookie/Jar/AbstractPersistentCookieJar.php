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
use Ivory\HttpAdapter\Event\Cookie\CookieInterface;

/**
 * Abstract persistent cookie jar.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractPersistentCookieJar extends CookieJar implements PersistentCookieJarInterface
{
    /**
     * Creates a persistent cookie jar.
     *
     * @param \Ivory\HttpAdapter\Event\Cookie\CookieFactoryInterface|null $cookieFactory The cookie factory.
     * @param boolean                                                     $load          TRUE if it should load the cookies else FALSE.
     */
    public function __construct(CookieFactoryInterface $cookieFactory = null, $load = true)
    {
        parent::__construct($cookieFactory);

        if ($load) {
            $this->load();
        }
    }

    /**
     * Destructs the persistent cookie jar.
     */
    public function __destruct()
    {
        $this->save();
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return json_encode(array_map(function (CookieInterface $cookie) {
            return $cookie->toArray();
        }, $this->getCookies()));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = json_decode($serialized, true);

        if (empty($data)) {
            $this->clear();
        } else {
            $cookieFactory = $this->getCookieFactory();

            $this->setCookies(array_map(function (array $cookie) use ($cookieFactory) {
                return $cookieFactory->create(
                    $cookie['name'],
                    $cookie['value'],
                    $cookie['attributes'],
                    $cookie['created_at']
                );
            }, $data));
        }
    }
}
