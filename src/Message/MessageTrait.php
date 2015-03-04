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
 * Message trait.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
trait MessageTrait
{
    /** @var array */
    private $parameters = array();

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameter($name)
    {
        return isset($this->parameters[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter($name)
    {
        return $this->hasParameter($name) ? $this->parameters[$name] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function withParameter($name, $value)
    {
        $new = clone $this;
        $new->parameters[$name] = $value;

        return $new;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function withoutParameter($name)
    {
        $new = clone $this;
        unset($new->parameters[$name]);

        return $new;
    }
}
