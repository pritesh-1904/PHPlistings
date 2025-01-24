<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Html;

class Attributes
    extends \App\Src\Support\Collection
{

    public function __toString()
    {
        $pairs = array();

        while ($this->valid()) {
            if (null !== $this->current()) {
                if (false !== is_bool($this->current())) {
                    if (false !== $this->current()) {
                        $pairs[] = $this->key();
                    }
                } else {
                    $pairs[] = $this->key() . '="' . $this->current() . '"'; 
                }
            }

            $this->next();
        }

        $this->rewind();

        return ' ' . implode(' ', $pairs);
    }

    public function add($name, $value, $append = false)
    {
        $this->put($name, (($this->offsetExists($name) && $append) ? trim($this->get($name) . ' ' . $value) : $value));
    }

    public function append($name, $value)
    {
        $this->add($name, $value, true);
    }

    public function has($name)
    {
        return $this->offsetExists($name);
    }

}
