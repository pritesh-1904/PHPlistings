<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Quadboxteaser
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
        'fourth_heading',
        'fourth_paragraph',
    ];

    public function isMultiInstance()
    {
        return true;
    }

    public function render()
    {        
        $this->rendered = true;

        return view('widgets/quad-box-teaser', [
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
            'center' => null,

            'first_image' => bin2hex(random_bytes(16)),
            'first_heading' => '{"en":"Add Listing"}',
            'first_paragraph' => '{"en":"Submit your business listing into the directory using an online submission tool."}',
            'first_link' => '',

            'second_image' => bin2hex(random_bytes(16)),
            'second_heading' => '{"en":"Claim Your Listing"}',
            'second_paragraph' => '{"en":"Request ownership of a listing by following a verification process."}',
            'second_link' => '',
            
            'third_image' => bin2hex(random_bytes(16)),
            'third_heading' => '{"en":"Contact Us To Get Listed"}',
            'third_paragraph' => '{"en":"Send us an email or give us a call to get your listing submitted by our experts."}',
            'third_link' => '',

            'fourth_image' => bin2hex(random_bytes(16)),
            'fourth_heading' => '{"en":"Get New Customers"}',
            'fourth_paragraph' => '{"en":"We will take care of increasing the number of your customers."}',
            'fourth_link' => '',

            'nofollow' => 1,
            'newwindow' => null,
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.quadboxteaser.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.twocolumnteaser.form.label.white'),
                'bg-light' => __('widget.twocolumnteaser.form.label.light'),
             ]])
            ->add('caption', 'translatable', ['label' => __('widget.quadboxteaser.form.label.caption')])
            ->add('heading', 'translatable', ['label' => __('widget.quadboxteaser.form.label.heading')])
            ->add('center', 'toggle', ['label' => __('widget.quadboxteaser.form.label.center')])

            ->add('first', 'separator')

            ->add('first_image', 'dropzone', ['label' => __('widget.quadboxteaser.form.label.first_image'), 'upload_id' => '30'])
            ->add('first_heading', 'translatable', ['label' => __('widget.quadboxteaser.form.label.first_heading')])
            ->add('first_paragraph', 'translatable', ['label' => __('widget.quadboxteaser.form.label.first_paragraph')])
            ->add('first_link', 'url', ['label' => __('widget.quadboxteaser.form.label.first_url')])

            ->add('second', 'separator')

            ->add('second_image', 'dropzone', ['label' => __('widget.quadboxteaser.form.label.second_image'), 'upload_id' => '30'])
            ->add('second_heading', 'translatable', ['label' => __('widget.quadboxteaser.form.label.second_heading')])
            ->add('second_paragraph', 'translatable', ['label' => __('widget.quadboxteaser.form.label.second_paragraph')])
            ->add('second_link', 'url', ['label' => __('widget.quadboxteaser.form.label.second_url')])

            ->add('third', 'separator')

            ->add('third_image', 'dropzone', ['label' => __('widget.quadboxteaser.form.label.third_image'), 'upload_id' => '30'])
            ->add('third_heading', 'translatable', ['label' => __('widget.quadboxteaser.form.label.third_heading')])
            ->add('third_paragraph', 'translatable', ['label' => __('widget.quadboxteaser.form.label.third_paragraph')])
            ->add('third_link', 'url', ['label' => __('widget.quadboxteaser.form.label.third_url')])

            ->add('fourth', 'separator')

            ->add('fourth_image', 'dropzone', ['label' => __('widget.quadboxteaser.form.label.fourth_image'), 'upload_id' => '30'])
            ->add('fourth_heading', 'translatable', ['label' => __('widget.quadboxteaser.form.label.fourth_heading')])
            ->add('fourth_paragraph', 'translatable', ['label' => __('widget.quadboxteaser.form.label.fourth_paragraph')])
            ->add('fourth_link', 'url', ['label' => __('widget.quadboxteaser.form.label.fourth_url')])

            ->add('other', 'separator')

            ->add('nofollow', 'toggle', ['label' => __('widget.quadboxteaser.form.label.nofollow')])
            ->add('newwindow', 'toggle', ['label' => __('widget.quadboxteaser.form.label.newwindow')]);    
    }

}
