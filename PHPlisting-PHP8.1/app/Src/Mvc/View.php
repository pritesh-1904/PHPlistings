<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Mvc;

class View
{

    protected $theme;

    public function __construct($theme)
    {
        $this->theme = trim($theme, DS) . DS;
    }

    public function render($template, $view = [], $extension = 'html.php')
    {
        if ($this->exists($this->theme . $template, $extension)) {
            if (!($view instanceof \App\Src\Support\BaseCollection)) {
                $view = collect($view);
            }

            ob_start();

            try {
                require $this->file($this->theme . $template, $extension);
            } catch (\Throwable $e) {
                ob_end_clean();
                throw $e;
            }

            return ob_get_clean();
        } else {
            throw new \Exception('Template file "' . $this->theme . $template . '" not found or is unreadable.');
        }
    }
   
    public function exists($template, $extension)
    {
        return (is_file($this->file($template, $extension)) && is_readable($this->file($template, $extension)));
    }

    protected function file($template, $extension)
    {
        return config()->view->path . DS . $template . '.' . $extension;
    }

}
