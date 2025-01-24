<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Cache\Driver;

class File
    implements \App\Src\Cache\Driver\DriverInterface
{

    public function get($offset, $default = null)
    {
        $files = glob(config()->cache->file->path . DS . md5($offset) . '*');

        if (false !== $files && is_array($files) && count($files) > 0) {
            if (false !== ($mtime = filemtime($files[0]))) {
                if (time() - $mtime <= (int) str_replace(md5($offset), '', basename($files[0]))) {
                    return unserialize(file_get_contents($files[0]));
                }
            }
        }

        return $default;
    }

    public function put($offset, $value, $seconds = 0)
    {        
        $file = fopen(config()->cache->file->path . DS . md5($offset) . $seconds, 'w');
        fwrite($file, serialize($value));
        fclose($file); 
    }

    public function has($offset)
    {
        $files = glob(config()->cache->file->path . DS . md5($offset) . '*');

        if (false !== $files && is_array($files) && count($files) > 0) {
            if (false !== ($mtime = filemtime($files[0]))) {
                if (time() - $mtime <= (int) str_replace(md5($offset), '', basename($files[0]))) {
                    return true;
                }
            }
        }

        return false;        
    }

    public function forget($offset)
    {
        $files = glob(config()->cache->file->path . DS . md5($offset) . '*');

        if (false !== $files && is_array($files) && count($files) > 0) {
            foreach ($files as $file) {
                if (is_file($file) && is_writable($file)) {
                    return unlink($file);
                }
            }
        }

        return false;
    }

}
