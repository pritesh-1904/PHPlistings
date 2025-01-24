<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Listingheader
    extends \App\Src\Widget\BaseWidget
{

    public function isMultiInstance()
    {
        return true;
    }

    public function render()
    {
        $widgets = $this->getWidgetizer()->all();

        foreach ($widgets as $widget) {
            if ($widget->slug == 'listingheader' && $widget->getWidgetObject()->isRendered()) {
                return null;
            }
        }

        if (null !== $this->getData()->get('listing') && $this->getData()->listing instanceof \App\Models\Listing) {
            $this->rendered = true;

            return view('widgets/listing-header', [
                'settings' => $this->getSettings(),
                'data' => $this->getData(),
            ]);
        }

        return null;
    }

    public function getDefaultSettings()
    {
        return collect([
            'colorscheme' => 'bg-light',
            'sharing' => 1,
            'bookmarking' => 1,
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.listingheader.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.listingheader.form.label.white'),
                'bg-light' => __('widget.listingheader.form.label.light'),
             ]])
            ->add('sharing', 'toggle', ['label' => __('widget.listingheader.form.label.sharing')])
            ->add('bookmarking', 'toggle', ['label' => __('widget.listingheader.form.label.bookmarking')]);
    }

}
