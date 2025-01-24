<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Http;

class FileResponse
    extends Response
{

    protected $file;
    protected $etag = null;

    public function __construct(\App\Src\Http\ResponseHeaders $headers)
    {
        $this->headers = $headers;
    }

    public function file($file)
    {
        if (!$file instanceof \App\Src\Http\File\File) {
            $this->file = new \App\Src\Http\File\File($file);
        } else {
            $this->file = $file;
        }

        return $this;
    }

    public function withEtag($etag)
    {
        $this->etag = $etag;

        return $this;
    }

    public function send($content = null)
    {
        if (null !== $this->etag && request()->server->get('HTTP_IF_NONE_MATCH') == $this->etag) {
            http_response_code(304);
        } else {        
            http_response_code($this->statusCode);

            if (null !== $this->etag) {
                $this->setHeader('ETag', $this->etag);
            }

            $this->setHeader('Content-Type', $this->file->getMimeType());
            $this->setHeader('Content-Length', $this->file->getSize());        
            $this->sendHeaders();

            $fp = fopen($this->file->getRealPath(), 'rb');
//            ob_end_clean();
            fpassthru($fp);
        }

        return $this;
    }

}
