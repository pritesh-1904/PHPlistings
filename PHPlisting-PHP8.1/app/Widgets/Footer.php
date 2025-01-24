<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Footer
    extends \App\Src\Widget\BaseWidget
{

    protected $translatable = [
        'about_heading',
        'about_paragraph',
        'menu_heading',
        'contact_heading',
        'contact_paragraph',
        'contact_address',
        'social_heading',
        'social_paragraph',
        'copyright',
    ];

    public function render()
    {
        $widgets = $this->getWidgetizer()->all();

        foreach ($widgets as $widget) {
            if ($widget->slug == 'footer' && $widget->getWidgetObject()->isRendered()) {
                return null;
            }
        }

        $this->rendered = true;

        return view('widgets/footer', [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'colorscheme' => 'bg-dark text-white',
            'about' => 1,
            'about_order' => 1,
            'about_heading' => '{"en":"About Us"}',
            'about_paragraph' => '{"en":"We help companies showcase their products and services online. Sign up today and start promoting your business."}',
            'menu' => null,
            'menu_order' => 2,
            'menu_heading' => '{"en":"Menu"}',
            'menu_group' => 2,
            'contact' => 1,
            'contact_order' => 3,
            'contact_heading' => '{"en":"Contact Us"}',
            'contact_paragraph' => '{"en":""}',
            'contact_address' => '{"en":"123 1st Avenue, Mountain View, CA, United States"}',
            'contact_phone' => '(123) 123-45-67 ext.123',
            'social' => 1,
            'social_order' => 4,
            'social_heading' => '{"en":"Follow Us"}',
            'social_paragraph' => '{"en":""}',

            'social_facebook' => 'https://facebook.com',
            'social_twitter' => 'https://twitter.com',
            'social_instagram' => 'https://instagram.com',
            'social_linkedin' => 'https://linkedin.com',
            'social_youtube' => 'https://youtube.com',
            'social_vimeo' => 'https://vimeo.com',
            'social_flickr' => 'https://flickr.com',
            'social_pinterest' => 'https://pinterest.com',

            'copyright' => '{"en":""}',
            'color' => 'red',
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.footer.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.footer.form.label.white'),
                'bg-light' => __('widget.footer.form.label.light'),
                'bg-dark text-white' => __('widget.footer.form.label.dark'),
             ]])
            ->add('about', 'toggle', ['label' => __('widget.footer.form.label.about')])
            ->add('about_order', 'select', ['label' => __('widget.footer.form.label.about_order'), 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4'], 'constraints' => 'required|number'])
            ->add('about_heading', 'translatable', ['label' => __('widget.footer.form.label.about_heading')])
            ->add('about_paragraph', 'translatable', ['label' => __('widget.footer.form.label.about_paragraph')])
            ->add('separator1', 'separator')
            ->add('menu', 'toggle', ['label' => __('widget.footer.form.label.menu')])
            ->add('menu_order', 'select', ['label' => __('widget.footer.form.label.menu_order'), 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4'], 'constraints' => 'required|number'])
            ->add('menu_heading', 'translatable', ['label' => __('widget.footer.form.label.menu_heading')])
            ->add('menu_group', 'select', ['label' => __('widget.footer.form.label.menu_group'), 'options' => \App\Models\WidgetMenuGroup::all()->pluck('name', 'id')->all(), 'constraints' => 'required|number'])
            ->add('separator2', 'separator')
            ->add('contact', 'toggle', ['label' => __('widget.footer.form.label.contact')])
            ->add('contact_order', 'select', ['label' => __('widget.footer.form.label.contact_order'), 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4'], 'constraints' => 'required|number'])
            ->add('contact_heading', 'translatable', ['label' => __('widget.footer.form.label.contact_heading')])
            ->add('contact_paragraph', 'translatable', ['label' => __('widget.footer.form.label.contact_paragraph')])
            ->add('contact_address', 'translatable', ['label' => __('widget.footer.form.label.contact_address')])
            ->add('contact_phone', 'text', ['label' => __('widget.footer.form.label.contact_phone')])
            ->add('separator3', 'separator')
            ->add('social', 'toggle', ['label' => __('widget.footer.form.label.social')])
            ->add('social_order', 'select', ['label' => __('widget.footer.form.label.social_order'), 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4'], 'constraints' => 'required|number'])
            ->add('social_heading', 'translatable', ['label' => __('widget.footer.form.label.social_heading')])
            ->add('social_paragraph', 'translatable', ['label' => __('widget.footer.form.label.social_paragraph')])
            ->add('social_facebook', 'url', ['label' => __('widget.footer.form.label.social_facebook')])
            ->add('social_twitter', 'url', ['label' => __('widget.footer.form.label.social_twitter')])
            ->add('social_instagram', 'url', ['label' => __('widget.footer.form.label.social_instagram')])
            ->add('social_linkedin', 'url', ['label' => __('widget.footer.form.label.social_linkedin')])
            ->add('social_youtube', 'url', ['label' => __('widget.footer.form.label.social_youtube')])
            ->add('social_vimeo', 'url', ['label' => __('widget.footer.form.label.social_vimeo')])
            ->add('social_flickr', 'url', ['label' => __('widget.footer.form.label.social_flickr')])
            ->add('social_pinterest', 'url', ['label' => __('widget.footer.form.label.social_pinterest')])
            ->add('separator4', 'separator')
            ->add('copyright', 'translatable', ['label' => __('widget.footer.form.label.copyright')]);
    }

}
