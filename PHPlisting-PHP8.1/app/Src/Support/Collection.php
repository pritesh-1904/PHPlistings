<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Support;

class Collection
    extends BaseCollection
{

    public function all()
    {
        return $this->toArray(false);
    }

    public function sum($offset) {
        $counter = 0;

        foreach ($this->items as $item) {
            $counter = $counter + $item->get($offset);
        }

        return $counter;
    }

    public function contains($offset, $value)
    {
        foreach ($this->items as $item) {
            if (isset($item[$offset]) && $item[$offset] == $value) {
                return true;
            }
        }        

        return false;
    }

    public function each(\Closure $callback)
    {
        foreach ($this->items as $key => $item) {
            if (false !== $return = $callback($item, $key)) {
                $this->put($key, $return);
            } else {
                break;
            }
        }

        return $this;
    }

    public function filter(\Closure $callback = null)
    {
        $filtered = new static;

        foreach ($this->items as $key => $item) {
            if (null === $callback || false !== $callback($item, $key)) {
                $filtered->put($key, $item);
            }
        }

        return $filtered;
    }

    public function first(\Closure $callback = null)
    {
        foreach ($this->filter($callback) as $item) {
            return $item;
        }

        return null;
    }

    public function last(\Closure $callback = null)
    {
        foreach ($this->reverse()->filter($callback) as $item) {
            return $item;
        }

        return null;
    }

    public function uasort(\Closure $callback)
    {
        $array = $this->items;
        $counter = 0;

        foreach ($array as &$item) {
            $item = [$counter++, $item];
        }

        uasort($array, function($a, $b) use ($callback) {$result = $callback($a[1], $b[1]); return $result == 0 ? $a[0] - $b[0] : $result;});

        foreach ($array as &$item) {
            $item = $item[1];
        }

        return new static($array);
    }

    public function slice($offset, $length = null)
    {
        return new static(array_slice($this->items, $offset, $length));
    }

    public function reverse($preserveKeys = true)
    {
        return new static(array_reverse($this->items, $preserveKeys));
    }

    public function pluck($offset, $keyOffset = null)
    {
        $array = [];
        $counter = 0;

        foreach ($this->items as $value) {
            $array[(null !== $keyOffset ? $value[$keyOffset] : $counter++)] = ($offset instanceof \Closure ? $offset($value) : $value[$offset]);
        }

        return new static($array);
    }

    public function implode($glue = '')
    {
        return implode($glue, $this->items);
    }

    public function reduce(\Closure $callback, $initial = null)
    {
        return array_reduce($this->items, $callback, $initial);
    }

    public function unique()
    {
        $filtered = [];

        foreach ($this->items as $key => $item) {
            if (false === in_array($item, $filtered)) {
                $filtered[$key] = $item;
            }
        }

        return new static($filtered);
    }

    public function where($offset, $operator, $value = null)
    {
        if (null === $value) {
            $value = $operator;
            $operator = '=';
        }

        return $this->filter(function ($item, $key) use ($offset, $value, $operator) {
            switch ($operator) {
                case '>':
                    return $item->get($offset) > $value;
                    break;
                case '<':
                    return $item->get($offset) < $value;
                    break;
                case '>=':
                    return $item->get($offset) >= $value;
                    break;
                case '<=':
                    return $item->get($offset) <= $value;
                    break;
                case '<>':
                case '!=':
                    return $item->get($offset) != $value;
                    break;
                default:
                    return $item->get($offset) == $value;
                    break;
            }
        });
    }
    
    public function merge(\App\Src\Support\Collection $collection)
    {
        return new static(array_merge($this->items, $collection->all()));
    }

    public function orderBy($column, $direction = 'asc', $locale = 'en_US.UTF-8')
    {
        $array = $this->items;

        if (false !== \function_exists('collator_compare')) {
            $collator = new \Collator($locale);
        
            usort($array, function ($a, $b) use ($collator, $column, $direction) {
                if ('asc' == $direction) {
                    return 1 === collator_compare($collator, trim(d($a[$column])), trim(d($b[$column]))) ? 1 : -1;
                } else {
                    return -1 === collator_compare($collator, trim(d($a[$column])), trim(d($b[$column]))) ? 1 : -1;
                }
            });
        } else {
            usort($array, function ($a, $b) use ($column, $direction) {
                if ('asc' == $direction) {
                    return (trim(strtolower($a[$column])) > trim(strtolower($b[$column]))) ? 1 : -1;
                } else {
                    return (trim(strtolower($a[$column])) < trim(strtolower($b[$column]))) ? 1 : -1;
                }
            });
        }

        return new static($array);
    }

}
