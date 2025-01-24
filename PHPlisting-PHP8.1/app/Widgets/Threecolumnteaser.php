<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Threecolumnteaser
    extends \App\Src\Widget\BaseWidget
{

    protected $translatable = [
        'caption',
        'heading',
        'first_heading',
        'first_paragraph',
        'second_heading',
        'second_paragraph',
        'third_heading',
        'third_paragraph',
    ];

    public function isMultiInstance()
    {
        return true;
    }

    public function render()
    {        
        $this->rendered = true;

        return view('widgets/three-column-teaser', [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'colorscheme' => 'bg-light',
            'caption' => '{"en":"Promote your business"}',
            'heading' => '{"en":"Advertise in Our Directory!"}',
            'first_icon' => 'fas fa-map-marked-alt',
            'first_heading' => '{"en":"Add Listing"}',
            'first_paragraph' => '{"en":"Submit your business listing into the directory using an online submission tool."}',
            'second_icon' => 'fas fa-shield-alt',
            'second_heading' => '{"en":"Claim Your Listing"}',
            'second_paragraph' => '{"en":"Request ownership of an existing listing by following a simple verification process."}',
            'third_icon' => 'fas fa-envelope',
            'third_heading' => '{"en":"Contact Us To Get Listed"}',
            'third_paragraph' => '{"en":"Send us an email or give us a call to get your listing submitted by our experts."}',
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.threecolumnteaser.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.threecolumnteaser.form.label.white'),
                'bg-light' => __('widget.threecolumnteaser.form.label.light'),
             ]])
            ->add('caption', 'translatable', ['label' => __('widget.threecolumnteaser.form.label.caption')])
            ->add('heading', 'translatable', ['label' => __('widget.threecolumnteaser.form.label.heading')])
            ->add('first_icon', 'icon', ['label' => __('widget.threecolumnteaser.form.label.first_icon')])
            ->add('first_heading', 'translatable', ['label' => __('widget.threecolumnteaser.form.label.first_heading')])
            ->add('first_paragraph', 'translatable', ['label' => __('widget.threecolumnteaser.form.label.first_paragraph')])
            ->add('second_icon', 'icon', ['label' => __('widget.threecolumnteaser.form.label.second_icon')])
            ->add('second_heading', 'translatable', ['label' => __('widget.threecolumnteaser.form.label.second_heading')])
            ->add('second_paragraph', 'translatable', ['label' => __('widget.threecolumnteaser.form.label.second_paragraph')])
            ->add('third_icon', 'icon', ['label' => __('widget.threecolumnteaser.form.label.third_icon')])
            ->add('third_heading', 'translatable', ['label' => __('widget.threecolumnteaser.form.label.third_heading')])
            ->add('third_paragraph', 'translatable', ['label' => __('widget.threecolumnteaser.form.label.third_paragraph')]);
    }

}
