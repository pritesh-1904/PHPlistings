<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Image
    extends \App\Src\Widget\BaseWidget
{

    protected $type = false;
    protected $translatable = [
        'alt',
        'caption',
    ];

    public function isMultiInstance()
    {
        return true;
    }

    public function render()
    {        
        $this->rendered = true;

        return view('widgets/image', [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'image' => bin2hex(random_bytes(16)),
            'alt' => '{"en":""}',
            'caption' => '{"en":""}',
            'caption_color' => '#FFFFFF',
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('image', 'dropzone', ['label' => __('widget.image.form.label.image'), 'upload_id' => '5'])
            ->add('alt', 'translatable', ['label' => __('widget.image.form.label.alt')])
            ->add('caption', 'translatable', ['label' => __('widget.image.form.label.caption')])
            ->add('caption_color', 'color', ['label' => __('widget.image.form.label.caption_color')]);
    }

}
