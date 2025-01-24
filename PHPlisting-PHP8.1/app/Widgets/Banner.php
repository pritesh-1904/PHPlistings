<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Banner
    extends \App\Src\Widget\BaseWidget
{

    public function isMultiInstance()
    {
        return true;
    }

    public function render()
    {        
        $this->rendered = true;

        return view('widgets/banner', [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'colorscheme' => 'bg-white',
            'block_1' => '',
            'block_2' => '',
            'block_3' => '',
            'block_4' => '',
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.banner.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.banner.form.label.white'),
                'bg-light' => __('widget.banner.form.label.light'),
             ]])
            ->add('block_1', 'textarea', ['label' => __('widget.banner.form.label.first_block')])
            ->add('block_2', 'textarea', ['label' => __('widget.banner.form.label.second_block')])
            ->add('block_3', 'textarea', ['label' => __('widget.banner.form.label.third_block')])
            ->add('block_4', 'textarea', ['label' => __('widget.banner.form.label.fourth_block')]);
    }

}
