<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Error405
    extends \App\Src\Widget\BaseWidget
{

    protected $translatable = [
        'heading',
        'description',
    ];

    public function render()
    {        
        $this->rendered = true;

        return view('widgets/error-405', [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'colorscheme' => 'bg-white',
            'heading' => '{"en":"405 Method Not Allowed"}',
            'description' => '{"en":"The HTTP method is recognizable but not supported."}',
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.error405.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.error405.form.label.white'),
                'bg-light' => __('widget.error405.form.label.light'),
             ]])
            ->add('heading', 'translatable', ['label' => __('widget.error405.form.label.heading')])
            ->add('description', 'translatable', ['label' => __('widget.error405.form.label.description')]);
    }

}
