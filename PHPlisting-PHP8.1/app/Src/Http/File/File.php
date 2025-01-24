<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Http\File;

class File
    extends \SplFileInfo
{

    public $magicFile = null;

    public function __construct($path)
    {
//        if (!is_file($path)) {
//            throw new FileNotFoundException($path);
//        }

        parent::__construct($path);        
    }

    public function getMimeType()
    {
        return (new \finfo(FILEINFO_MIME_TYPE, $this->magicFile))->file($this->getRealPath());
    }

}
