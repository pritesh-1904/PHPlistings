<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Custom
    extends \App\Src\Widget\BaseWidget
{

    public function isMultiInstance()
    {
        return true;
    }

    public function render()
    {        
        $this->rendered = true;

        return view('widgets/custom', [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'colorscheme' => 'bg-white',
            'title' => '',
            'description' => '',
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.custom.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.custom.form.label.white'),
                'bg-light' => __('widget.custom.form.label.light'),
             ]])
            ->add('title', 'text', ['label' => __('widget.custom.form.label.title')])
            ->add('description', 'htmltextarea', ['label' => __('widget.custom.form.label.description'), 'config' => 'advanced']);
    }

}
