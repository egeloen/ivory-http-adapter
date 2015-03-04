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
    /** @var string|null */
    private $name;

    /** @var string|null */
    private $value;

    /** @var array */
    private $attributes;

    /** @var integer */
    private $createdAt;

    /**
     * Creates a cookie.
     *
     * @param string|null $name       The name.
     * @param string|null $value      The value.
     * @param array       $attributes The attributes.
     * @param integer     $createdAt  The creation date (unix timestamp).
     */
    public function __construct($name, $value, array $attributes, $createdAt)
    {
        $this->setName($name);
        $this->setValue($value);
        $this->setAttributes($attributes);
        $this->setCreatedAt($createdAt);
    }

    /**
     * {@inheritdoc}
     */
    public function hasName()
    {
        return $this->name !== null;
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
    public function hasValue()
    {
        return $this->value !== null;
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
    public function getExpires()
    {
        if ($this->hasAttribute(self::ATTR_EXPIRES)) {
            return strtotime($this->getAttribute(self::ATTR_EXPIRES));
        }

        if ($this->hasAttribute(self::ATTR_MAX_AGE)) {
            return $this->createdAt + $this->getAttribute(self::ATTR_MAX_AGE);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isExpired()
    {
        return ($expires = $this->getExpires()) !== false ? $expires < time() : false;
    }

    /**
     * {@inheritdoc}
     */
    public function compare(CookieInterface $cookie)
    {
        return $this->name === $cookie->getName()
            && $this->getAttribute(self::ATTR_DOMAIN) === $cookie->getAttribute(self::ATTR_DOMAIN)
            && $this->getAttribute(self::ATTR_PATH) === $cookie->getAttribute(self::ATTR_PATH);
    }

    /**
     * {@inheritdoc}
     */
    public function match(InternalRequestInterface $request)
    {
        return $this->matchDomain($request->getUri()->getHost())
            && $this->matchPath($request->getUri()->getPath())
            && $this->matchScheme($request->getUri()->getScheme());
    }

    /**
     * {@inheritdoc}
     */
    public function matchDomain($domain)
    {
        if (!$this->hasAttribute(self::ATTR_DOMAIN)) {
            return true;
        }

        $cookieDomain = $this->getAttribute(self::ATTR_DOMAIN);

        if (strpos($cookieDomain, '.') === 0) {
            return (bool) preg_match('/\b'.preg_quote(substr($cookieDomain, 1), '/').'$/i', $domain);
        }

        return strcasecmp($cookieDomain, $domain) === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function matchPath($path)
    {
        if (!$this->hasAttribute(self::ATTR_PATH)) {
            return true;
        }

        return strpos($path, $this->getAttribute(self::ATTR_PATH)) === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function matchScheme($scheme)
    {
        if (!$this->hasAttribute(self::ATTR_SECURE)) {
            return true;
        }

        $secure = $this->getAttribute(self::ATTR_SECURE);

        return ($secure && $scheme === 'https') || (!$secure && ($scheme === 'http' || $scheme === null));
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array(
            'name'       => $this->name,
            'value'      => $this->value,
            'attributes' => $this->attributes,
            'created_at' => $this->createdAt,
        );
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
    private function fixAttribute($attribute)
    {
        return strtolower(trim($attribute));
    }
}
