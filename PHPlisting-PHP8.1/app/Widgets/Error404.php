<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Error404
    extends \App\Src\Widget\BaseWidget
{

    protected $translatable = [
        'heading',
        'description',
    ];

    public function render()
    {        
        $this->rendered = true;

        return view('widgets/error-404', [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'colorscheme' => 'bg-white',
            'heading' => '{"en":"404 Page Not Found"}',
            'description' => '{"en":"The requested URL was not found on the server."}',
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.error404.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.error404.form.label.white'),
                'bg-light' => __('widget.error404.form.label.light'),
             ]])
            ->add('heading', 'translatable', ['label' => __('widget.error404.form.label.heading')])
            ->add('description', 'translatable', ['label' => __('widget.error404.form.label.description')]);
    }

}
