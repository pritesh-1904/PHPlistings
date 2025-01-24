<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Twocolumnteaser
    extends \App\Src\Widget\BaseWidget
{

    protected $translatable = [
        'caption',
        'heading',
        'paragraph',
    ];

    public function isMultiInstance()
    {
        return true;
    }

    public function render()
    {        
        $this->rendered = true;

        return view('widgets/two-column-teaser', [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'colorscheme' => 'bg-light',
            'image' => bin2hex(random_bytes(16)),
            'caption' => '{"en":"Promote your business"}',
            'heading' => '{"en":"Advertise in Our Directory!"}',
            'paragraph' => '{"en":"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."}',
            'button' => '&lt;a class=&quot;btn btn-lg btn-round btn-primary&quot; href=&quot;#&quot; role=&quot;button&quot;&gt;Get Started&lt;/a&gt;',
            'image_order' => '1',
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.twocolumnteaser.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.twocolumnteaser.form.label.white'),
                'bg-light' => __('widget.twocolumnteaser.form.label.light'),
             ]])
            ->add('image', 'dropzone', ['label' => __('widget.twocolumnteaser.form.label.image'), 'upload_id' => '20'])
            ->add('caption', 'translatable', ['label' => __('widget.twocolumnteaser.form.label.caption')])
            ->add('heading', 'translatable', ['label' => __('widget.twocolumnteaser.form.label.heading')])
            ->add('paragraph', 'translatable', ['label' => __('widget.twocolumnteaser.form.label.paragraph')])
            ->add('button', 'textarea', ['label' => __('widget.twocolumnteaser.form.label.button')])
            ->add('image_order', 'select', ['label' => __('widget.twocolumnteaser.form.label.image_order'), 'options' => ['1' => '1', '2' => '2'], 'constraints' => 'number']);
    }

}
