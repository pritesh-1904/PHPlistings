<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Repositories;

class SitemapIndex
{

    private $file;

    public function __construct()
    {
        $this->openFile();
    }

    public function push($type, $fileCount, $template)
    {
        for ($i = 1; $i <= $fileCount; $i++) {        
            $data = 
                '   <sitemap>' . "\n" . 
                '       <loc>' . route('sitemap/' . $type . '/' . $this->guessFileName($template, $i)) . '</loc>' . "\n" . 
                '   </sitemap>' . "\n";

            $this->write($data);
        }

        return $this;
    }

    public function render()
    {
        return $this->closeFile();
    }

    private function openFile()
    {
        $this->file = fopen(PATH . DS . 'sitemap.xml', 'w');

        $header = 
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . 
            '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        if (false !== $this->file) {
            $this->write($header);
        }
        
        return $this->file;
    }

    private function closeFile()
    {
        $footer = '</sitemapindex>';

        $this->write($footer);

        return fclose($this->file);
    }

    private function write($data)
    {
        return fputs($this->file, $data);
    }

    private function guessFileName($template, $count)
    {
        return str_replace('{count}', ($count > 1 ? $count : ''), $template);
    }

}
