<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Slider
    extends \App\Src\Widget\BaseWidget
{

    public function isMultiInstance()
    {
        return true;
    }

    public function render()
    {        
        $this->rendered = true;

        return view('widgets/slider', [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'slider' => bin2hex(random_bytes(16)),
            'slider_slides_per_view' => '3',
            'slider_space_between' => '0',
            'slider_speed' => '2000',
            'slider_autoplay' => 1,
            'slider_autoplay_delay' => '10000',
            'slider_navigation' => 1,
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('slider', 'dropzone', ['label' => __('widget.slider.form.label.slider'), 'upload_id' => '2'])
            ->add('slider_slides_per_view', 'number', ['label' => __('widget.slider.form.label.slider_slides_per_view'), 'constraints' => 'required|min:1|max:3'])
            ->add('slider_space_between', 'number', ['label' => __('widget.slider.form.label.slider_space_between'), 'constraints' => 'required'])
            ->add('slider_speed', 'number', ['label' => __('widget.slider.form.label.slider_transition'), 'constraints' => 'required'])
            ->add('slider_autoplay', 'toggle', ['label' => __('widget.slider.form.label.slider_autoplay')])
            ->add('slider_autoplay_delay', 'number', ['label' => __('widget.slider.form.label.slider_autoplay_delay'), 'constraints' => 'required'])
            ->add('slider_navigation', 'toggle', ['label' => __('widget.slider.form.label.slider_navigation')]);
    }

}
