<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Newsletter
    extends \App\Src\Widget\BaseWidget
{

    protected $translatable = [
        'heading',
        'paragraph',
    ];

    public function render()
    {        
        $this->rendered = true;
        
        return view('widgets/newsletter', [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'image' => bin2hex(random_bytes(16)),
            'heading' => '{"en":"Subscribe to Our Newsletter"}',
            'paragraph' => '{"en":"Get directory news and updates to your inbox!"}',
            'button' => '&lt;a class=&quot;btn btn-lg btn-round btn-primary&quot; href=&quot;#&quot; role=&quot;button&quot;&gt;Subscribe&lt;/a&gt;',
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('image', 'dropzone', ['label' => __('widget.newsletter.form.label.image'), 'upload_id' => '5'])
            ->add('heading', 'translatable', ['label' => __('widget.newsletter.form.label.heading')])
            ->add('paragraph', 'translatable', ['label' => __('widget.newsletter.form.label.paragraph')])
            ->add('button', 'textarea', ['label' => __('widget.newsletter.form.label.button')]);
    }

}
