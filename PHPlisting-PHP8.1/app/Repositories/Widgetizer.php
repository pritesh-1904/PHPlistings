<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Repositories;

class Widgetizer
    extends \App\Src\Support\Collection
{

    public function push($widget)
    {       
        $widget->setWidgetizer($this);

        parent::push($widget);
    }

    public function render($data = null)
    {       
        $content = '';

        if (auth()->check('admin_login')) {
            $content .= view('misc/toolbar', ['data' => $data]);
        }

        foreach ($this as $widget) {
            $widget->compile();
        }

        foreach ($this->all() as $widget) {
            $response = $widget->render();

            if ($response instanceof \App\Src\Http\Response) {
                return $response;
            }

            if (false !== auth()->check('admin_login') && false !== auth()->check('admin_appearance')) {
                $response = view('misc/widget-management', ['content' => $response, 'page' => $data->page, 'widget' => $widget->getWidgetObject()]);
            }

            $content .= $response;
        }

        return $content;
    }

}
