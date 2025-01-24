<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Support;

class Container
    extends Collection
{

    private $factories;

    public function factory(\Closure $callable)
    {
        if (null === $this->factories) {
            $this->factories = new \SplObjectStorage;        
        }

        $this->factories->attach($callable);

        return $callable;
    }

    public function offsetGet($offset)
    {
        if ($this->get($offset) instanceof \Closure) {
            if (isset($this->factories[$this->get($offset)])) {
                return $this->get($offset)($this);
            }

            $this->put($offset, $this->get($offset)($this));
        }

        return $this->get($offset);
    }

}
