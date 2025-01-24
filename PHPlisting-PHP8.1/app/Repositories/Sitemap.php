<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Repositories;

class Sitemap
{

    private $type;
    private $file;
    private $template = 'sitemap{count}.xml';
    private $recordLimit = 10000;
    private $recordCount = 0;
    private $fileCount = 0;

    public function __construct($type, \App\Repositories\SitemapIndex $index)
    {
        $this->type = $type;
        $this->index = $index;

        $this->openFile();
    }

    public function push($loc, $lastmod = null, $changefreq = 'weekly', $priority = 0.5)
    {
        $data = 
            '   <url>' . "\n" . 
            '       <loc>' . $loc . '</loc>' . "\n" . 
            '       <lastmod>' . locale()->formatDatetimeISO8601($lastmod ?? date('Y-m-d H:i:s')) . '</lastmod>' . "\n" . 
            '       <changefreq>' . $changefreq . '</changefreq>' . "\n" . 
            '       <priority>' . $priority . '</priority>' . "\n" . 
            '   </url>' . "\n";

        $response = $this->write($data);

        $this->recordCount++;

        if ($this->recordCount == $this->recordLimit) {
            $this->closeFile();
            $this->openFile();
        }

        return $response;
    }

    public function render()
    {
        $this->index->push($this->type, $this->getFileCount(), $this->getTemplate());

        return $this->closeFile();
    }

    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function getFileCount()
    {
        return $this->fileCount;
    }

    public function getType()
    {
        return $this->type;
    }

    private function openFile()
    {
        $this->recordCount = 0;
        $this->fileCount++;

        $this->file = fopen(PATH . DS . 'sitemap' . DS . $this->type . DS . $this->guessFileName(), 'w');

        $header = 
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . 
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        if (false !== $this->file) {
            $this->write($header);
        }
        
        return $this->file;
    }

    private function closeFile()
    {
        $footer = '</urlset>';

        $this->write($footer);

        return fclose($this->file);
    }

    private function write($data)
    {
        return fputs($this->file, $data);
    }

    private function guessFileName()
    {
        return str_replace('{count}', ($this->fileCount > 1 ? $this->fileCount : ''), $this->template);
    }

}
