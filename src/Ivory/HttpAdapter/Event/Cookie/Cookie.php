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
 * {@inheritdoc}
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Cookie implements CookieInterface
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $value;

    /** @var array */
    protected $attributes;

    /** @var integer */
    protected $createdAt;

    /**
     * Creates a cookie.
     *
     * @param string  $name       The name.
     * @param string  $value      The value.
     * @param array   $attributes The attributes.
     * @param integer $createdAt  The creation date (unix timestamp).
     */
    public function __construct($name, $value, array $attributes, $createdAt)
    {
        $this->setName($name);
        $this->setValue($value);
        $this->setAttributes($attributes);
        $this->setCreatedAt($createdAt);

        if (!$this->hasAttribute(self::ATTR_SECURE)) {
            $this->setAttribute(self::ATTR_SECURE, false);
        }
    }

    /**
     * Parses a cookie header.
     *
     * @param string $header The cookie header.
     *
     * @return array The parsed cookie header (0 => name, 1 => value, 2 => attributes).
     */
    public static function parse($header)
    {
        list($name, $header) = explode('=', $header, 2);

        if (strpos($header, ';') === false) {
            $value = $header;
            $header = null;
        } else {
            list($value, $header) = explode(';', $header, 2);
        }

        $attributes = array();
        foreach (explode(';', $header) as $pair) {
            if (empty($pair)) {
                continue;
            }

            if (strpos($pair, '=') === false) {
                $attributeName = $pair;
                $attributeValue = null;
            } else {
                list($attributeName, $attributeValue) = explode('=', $pair);
            }

            $attributes[trim($attributeName)] = $attributeValue ? trim($attributeValue) : true;
        }

        return array(trim($name), trim($value), $attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function clearAttributes()
    {
        $this->attributes = array();
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttributes()
    {
        return !empty($this->attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributes(array $attributes)
    {
        $this->clearAttributes();
        $this->addAttributes($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function addAttributes(array $attributes)
    {
        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAttributes(array $names)
    {
        foreach ($names as $name) {
            $this->removeAttribute($name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttribute($name)
    {
        return isset($this->attributes[$this->fixAttribute($name)]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($name)
    {
        return $this->hasAttribute($name) ? $this->attributes[$this->fixAttribute($name)] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$this->fixAttribute($name)] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAttribute($name)
    {
        unset($this->attributes[$this->fixAttribute($name)]);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getAge()
    {
        return time() - $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function isExpired()
    {
        if ($this->hasAttribute(self::ATTR_MAX_AGE) && ($this->getAge() > $this->getAttribute(self::ATTR_MAX_AGE))) {
            return true;
        }

        if ($this->hasAttribute(self::ATTR_EXPIRES) && (strtotime($this->getAttribute(self::ATTR_EXPIRES)) < time())) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function match(InternalRequestInterface $request)
    {
        return $this->matchDomain($request) && $this->matchPath($request) && $this->matchSecure($request);
    }

    /**
     * {@inheritdoc}
     */
    public function matchDomain(InternalRequestInterface $request)
    {
        if (!$this->hasAttribute(self::ATTR_DOMAIN)) {
            return true;
        }

        $cookieDomain = $this->getAttribute(self::ATTR_DOMAIN);
        $domain = parse_url($request->getUrl(), PHP_URL_HOST);

        if (strpos($cookieDomain, '.') === 0) {
            return (bool) preg_match('/\b'.preg_quote(substr($cookieDomain, 1), '/').'$/i', $domain);
        }

        return strcasecmp($cookieDomain, $domain) === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function matchPath(InternalRequestInterface $request)
    {
        if (!$this->hasAttribute(self::ATTR_PATH)) {
            return true;
        }

        return strpos(parse_url($request->getUrl(), PHP_URL_PATH), $this->getAttribute(self::ATTR_PATH)) === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function matchSecure(InternalRequestInterface $request)
    {
        if (!$this->hasAttribute(self::ATTR_SECURE)) {
            return true;
        }

        $secure = $this->getAttribute(self::ATTR_SECURE);
        $scheme = parse_url($request->getUrl(), PHP_URL_SCHEME);

        return ($secure && $scheme === 'https') || (!$secure && (($scheme === 'http') || empty($scheme)));
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->name.'='.$this->value;
    }

    /**
     * Fixes the attribute.
     *
     * @param string $attribute The attribute.
     *
     * @return string The fixes attribute.
     */
    protected function fixAttribute($attribute)
    {
        return strtolower(trim($attribute));
    }
}
