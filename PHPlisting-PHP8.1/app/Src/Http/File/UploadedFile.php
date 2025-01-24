<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Http\File;

class UploadedFile
    extends File
{

    protected $realname;
    protected $mime;
    protected $size;
    protected $error;

    public function __construct($path, $realname, $mime, $size, $error)
    {
//        if (!is_uploaded_file($path)) {
//            throw new FileNotFoundException($path);
//        }

        $this->realname = $realname;
        $this->mime = $mime;
        $this->size = $size;
        $this->error = $error;

        parent::__construct($path);
    }

    public function getClientFilename()
    {
        return $this->realname;
    }

    public function getClientMimeType()
    {
        return $this->mime;
    }

    public function getClientSize()
    {
        return $this->size;
    }

    public function getClientError()
    {
        return $this->error;
    }

    public function store($path)
    {
        return move_uploaded_file($this->getPathname(), $path);
    }

}
