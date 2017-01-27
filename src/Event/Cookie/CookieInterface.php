<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\Cookie;

use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface CookieInterface
{
    const ATTR_DOMAIN = 'domain';
    const ATTR_PATH = 'path';
    const ATTR_SECURE = 'secure';
    const ATTR_MAX_AGE = 'max-age';
    const ATTR_EXPIRES = 'expires';

    /**
     * @return bool
     */
    public function hasName();

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string|null
     */
    public function setName($name);

    /**
     * @return bool
     */
    public function hasValue();

    /**
     * @return string|null
     */
    public function getValue();

    /**
     * @param string|null $value
     */
    public function setValue($value);

    public function clearAttributes();

    /**
     * @return bool
     */
    public function hasAttributes();

    /**
     * @return array
     */
    public function getAttributes();

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes);

    /**
     * @param array $attributes
     */
    public function addAttributes(array $attributes);

    /**
     * @param array $names
     */
    public function removeAttributes(array $names);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasAttribute($name);

    /**
     * @param string $name
     *
     * @return string
     */
    public function getAttribute($name);

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setAttribute($name, $value);

    /**
     * @param string $name
     */
    public function removeAttribute($name);

    /**
     * @return int
     */
    public function getCreatedAt();

    /**
     * @param int $createdAt
     */
    public function setCreatedAt($createdAt);

    /**
     * @return int|bool
     */
    public function getExpires();

    /**
     * @return bool
     */
    public function isExpired();

    /**
     * @param CookieInterface $cookie
     *
     * @return bool
     */
    public function compare(CookieInterface $cookie);

    /**
     * @param InternalRequestInterface $request
     *
     * @return bool
     */
    public function match(InternalRequestInterface $request);

    /**
     * @param string|null $domain
     *
     * @return bool
     */
    public function matchDomain($domain);

    /**
     * @param string|null $path
     *
     * @return bool
     */
    public function matchPath($path);

    /**
     * @param string|null $scheme
     *
     * @return bool
     */
    public function matchScheme($scheme);

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return string
     */
    public function __toString();
}
