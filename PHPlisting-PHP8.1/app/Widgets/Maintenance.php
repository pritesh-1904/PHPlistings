<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Maintenance
    extends \App\Src\Widget\BaseWidget
{

    protected $translatable = [
        'heading',
        'description',
    ];

    public function render()
    {        
        $this->rendered = true;

        return view('widgets/maintenance', [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'colorscheme' => 'bg-white',
            'heading' => '{"en":"Maintenance Mode"}',
            'description' => '{"en":"The page is currently down for maintenance."}',
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.maintenance.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.maintenance.form.label.white'),
                'bg-light' => __('widget.maintenance.form.label.light'),
             ]])
            ->add('heading', 'translatable', ['label' => __('widget.maintenance.form.label.heading')])
            ->add('description', 'translatable', ['label' => __('widget.maintenance.form.label.description')]);
    }

}
