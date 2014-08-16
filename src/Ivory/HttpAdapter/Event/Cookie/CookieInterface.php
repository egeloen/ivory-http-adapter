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
 * Cookie.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface CookieInterface
{
    /** @const string The domain attribute. */
    const ATTR_DOMAIN = 'domain';

    /** @const string The path attribute. */
    const ATTR_PATH = 'path';

    /** @const string The secure attribute. */
    const ATTR_SECURE = 'secure';

    /** @const string The max age attribute. */
    const ATTR_MAX_AGE = 'max-age';

    /** @const string The expires attribute. */
    const ATTR_EXPIRES = 'expires';

    /**
     * Gets the name.
     *
     * @return string The name.
     */
    public function getName();

    /**
     * Sets the name.
     *
     * @param string $name The name.
     */
    public function setName($name);

    /**
     * Gets the value.
     *
     * @return string The value.
     */
    public function getValue();

    /**
     * Sets the value.
     *
     * @param string $value The value.
     */
    public function setValue($value);

    /**
     * Clears the attributes.
     */
    public function clearAttributes();

    /**
     * Checks if there are attributes.
     *
     * @return boolean TRUE if there are attributes else FALSE.
     */
    public function hasAttributes();

    /**
     * Gets the attributes.
     *
     * @return array The attributes.
     */
    public function getAttributes();

    /**
     * Sets the attributes.
     *
     * @param array $attributes The attributes.
     */
    public function setAttributes(array $attributes);

    /**
     * Adds the attributes.
     *
     * @param array $attributes The attributes.
     */
    public function addAttributes(array $attributes);

    /**
     * Removes the attributes.
     *
     * @param array $names The attribute names.
     */
    public function removeAttributes(array $names);

    /**
     * Checks if there is an attribute.
     *
     * @param string $name The attribute name.
     *
     * @return boolean TRUE if there is the attribute else FALSE.
     */
    public function hasAttribute($name);

    /**
     * Gets an attribute value.
     *
     * @param string $name The attribute name.
     *
     * @return string The attribute value.
     */
    public function getAttribute($name);

    /**
     * Sets an attribute.
     *
     * @param string $name  The attribute name.
     * @param mixed  $value The attribute value.
     */
    public function setAttribute($name, $value);

    /**
     * Removes an attribute.
     *
     * @param string $name The attribute name.
     */
    public function removeAttribute($name);

    /**
     * Gets the creation date (unix timestamp).
     *
     * @return integer The creation date.
     */
    public function getCreatedAt();

    /**
     * Sets the creation date (unix timestamp).
     *
     * @param integer $createdAt The creation date (unix timestamp).
     */
    public function setCreatedAt($createdAt);

    /**
     * Gets the age.
     *
     * @return integer The age.
     */
    public function getAge();

    /**
     * Checks if it is expired.
     *
     * @return boolean TRUE if it is expired else FALSE.
     */
    public function isExpired();

    /**
     * Checks if it matches the request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request The request.
     *
     * @return boolean TRUE if it matches the request else FALSE.
     */
    public function match(InternalRequestInterface $request);

    /**
     * Checks if it matches the domain.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request The request.
     *
     * @return boolean TRUE if it matches the domain else FALSE.
     */
    public function matchDomain(InternalRequestInterface $request);

    /**
     * Checks if it matches the path.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request The request.
     *
     * @return boolean TRUE if it matches the path else FALSE.
     */
    public function matchPath(InternalRequestInterface $request);

    /**
     * Checks if it matches the secure.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request The request.
     *
     * @return boolean TRUE if it matches the secure else FALSE.
     */
    public function matchSecure(InternalRequestInterface $request);

    /**
     * Converts the cookie to string.
     *
     * @return string The converted cookie.
     */
    public function __toString();
}
