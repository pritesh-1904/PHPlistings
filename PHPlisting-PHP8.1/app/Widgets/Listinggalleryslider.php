<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Listinggalleryslider
    extends \App\Src\Widget\BaseWidget
{

    public function render()
    {
        if (null === $this->getData()->get('listing') || false === ($this->getData()->get('listing') instanceof \App\Models\Listing)) {
            return null;
        }

        layout()->addCss('<link href="' . asset('js/swiper/css/swiper.min.css?v=844') . '" rel="stylesheet">');
        layout()->addFooterJs('<script src="' . asset('js/swiper/js/swiper.min.js?v=844') . '"></script>');

        $this->rendered = true;

        return view('widgets/listing-gallery-slider', [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
            'images' => $this->getImages(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'slider_speed' => '2000',
            'slider_autoplay' => 1,
            'slider_autoplay_delay' => '5000',
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('slider_speed', 'number', ['label' => __('widget.listinggalleryslider.form.label.slider_transition'), 'constraints' => 'required'])
            ->add('slider_autoplay', 'toggle', ['label' => __('widget.listinggalleryslider.form.label.slider_autoplay')])
            ->add('slider_autoplay_delay', 'number', ['label' => __('widget.listinggalleryslider.form.label.slider_autoplay_delay'), 'constraints' => 'required']);
    }

    public function getImages()
    {
        if ($this->getData()->listing->_gallery_size > 0) {
            $field = $this->getData()->listing->data->where('field_name', 'gallery_id')->first();

            if (null !== $field->value && '' != $field->value) {
                $gallery = \App\Models\File::where('document_id', $field->value)->limit((int) $this->getData()->listing->_gallery_size)->get();

                if ($gallery->count() > 0) {
                    return $gallery;
                }
            }
        }

        return collect();
    }

}
