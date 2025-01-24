<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Support;

class BaseCollection
    implements \ArrayAccess, \Iterator, \Countable
{

    protected $items = [];

    public function __construct(array $array = null, $recursive = false)
    {
        if (null !== $array) {
            $this->collect($array, $recursive);
        }
    }

    public function collect(array $array, $recursive = false)
    {
        foreach ($array as $key => $value) {
            if (is_array($value) && $recursive) {
                $this->put($key, (new static)->collect($value, true));
            } else {
                $this->put($key, $value);
            }
        }

        return $this;
    }

    public function toArray($recursive = true)
    {
        $array = [];

        foreach ($this->items as $key => $value) {
            if ($recursive && ($value instanceof self)) {
                $array[$key] = $value->toArray(true);
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }

    public function toJson()
    {
        return json_encode($this->toArray(true));
    }

    public function get($offset, $default = null)
    {
        return $this->items[$offset] ?? $default;
    }

    public function put($offset, $value)
    {
        $this->items[$offset] = $value;

        return $this;
    }

    public function push($value)
    {
        $this->items[] = $value;

        return $this;
    }

    public function has($offset)
    {
        return array_key_exists($offset, $this->items);
    }

    public function forget($offset)
    {
        $this->offsetUnset($offset);

        return $this;
    }

    public function reverse()
    {
        return collect(array_reverse($this->items, true));
    }

    public function pop()
    {
        return array_pop($this->items);
    }

    public function isEmpty()
    {
        return count($this->items) == 0;
    }

    public function offsetSet($offset, $value): void
    {
        if (null === $offset) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->items);
    }

    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function rewind(): void
    {
        reset($this->items);
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        return current($this->items);
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return key($this->items);
    }

    public function next(): void
    {
        next($this->items);
    }

    public function valid(): bool
    {
        return $this->offsetExists(key($this->items));
    }

    public function __set($offset, $value)
    {
        $this->offsetSet($offset, $value);
    }

    public function __isset($offset)
    {
        return $this->offsetExists($offset);
    }

    public function __unset($offset)
    {
        return $this->offsetUnset($offset);
    }

    public function __get($offset)
    {
        return $this->offsetGet($offset);
    }

}
