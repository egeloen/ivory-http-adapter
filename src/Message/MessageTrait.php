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
 * @author GeLo <geloen.eric@gmail.com>
 */
trait MessageTrait
{
    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasParameter($name)
    {
        return isset($this->parameters[$name]);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getParameter($name)
    {
        return $this->hasParameter($name) ? $this->parameters[$name] : null;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return object
     */
    public function withParameter($name, $value)
    {
        $new = clone $this;
        $new->parameters[$name] = $value;

        return $new;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return object
     */
    public function withAddedParameter($name, $value)
    {
        $new = clone $this;
        $new->parameters[$name] = $new->hasParameter($name)
            ? array_merge((array) $new->parameters[$name], (array) $value)
            : $value;

        return $new;
    }

    /**
     * @param string $name
     *
     * @return object
     */
    public function withoutParameter($name)
    {
        $new = clone $this;
        unset($new->parameters[$name]);

        return $new;
    }
}
