<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Header
    extends \App\Src\Widget\BaseWidget
{

    public function render()
    {
        $widgets = $this->getWidgetizer()->all();

        foreach ($widgets as $widget) {
            if ($widget->slug == 'header' && $widget->getWidgetObject()->isRendered()) {
                return null;
            }
        }

        $this->rendered = true;

        return view('widgets/header', [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'colorscheme' => 'navbar-light bg-white',
            'logo' => bin2hex(random_bytes(16)),
            'menu_group' => 1,
            'button' => 1,
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.header.form.label.colorscheme'), 'options' => [
                'navbar-light bg-white' => __('widget.header.form.label.white'),
                'navbar-light bg-light' => __('widget.header.form.label.light'),
                'navbar-dark bg-dark' => __('widget.header.form.label.dark'),
             ]])
            ->add('logo', 'dropzone', ['label' => __('widget.header.form.label.logo'), 'upload_id' => '7'])
            ->add('menu_group', 'select', ['label' => __('widget.header.form.label.menu_group'), 'options' => \App\Models\WidgetMenuGroup::all()->pluck('name', 'id')->all(), 'constraints' => 'required|number'])
            ->add('button', 'toggle', ['label' => __('widget.header.form.label.button')]);
    }

}
