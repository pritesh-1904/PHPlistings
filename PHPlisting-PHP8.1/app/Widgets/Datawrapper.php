<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Datawrapper
    extends \App\Src\Widget\BaseWidget
{

    public function render()
    {        
        $widgets = $this->getWidgetizer()->all();

        foreach ($widgets as $widget) {
            if ($widget->slug == 'datawrapper' && $widget->getWidgetObject()->isRendered()) {
                return null;
            }
        }

        $this->rendered = true;

        return view('widgets/data-wrapper', [
            'data' => $this->getData()->html ?? '',
        ]);
    }

    public function getForm()
    {
        return form();
    }

}
