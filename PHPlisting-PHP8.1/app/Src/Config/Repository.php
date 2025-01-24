<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Config;

class Repository
    extends \App\Src\Support\Collection
{

    public function __construct($path)
    {
        if (!is_dir($path) || !is_readable($path)) {
            throw new \Exception('Configuration path is invalid.');
        }

        foreach ($this->getFileList($path) as $file) {
            if (1 === preg_match('/^(.*)\.php$/', $file, $match)) {
                if (isset($match[1])) {
                    $this->put(strtolower($match[1]), collect(include $path . DS . $file, true));
                }
            }
        }
    }    

    protected function getFileList($path)
    {
        return array_diff(scandir($path), ['.', '..']);
    }

    public function offsetUnset($offset): void
    {
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            if (null !== $group = \App\Models\SettingGroup::where('slug', $offset)->first()) {
                $this->put($offset, new parent($group->settings()->get()->pluck('value', 'name')->all()));
            }
        }

        return $this->get($offset);
    }

}
