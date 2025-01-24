<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Addaccountteaser
    extends \App\Src\Widget\BaseWidget
{

    protected $translatable = [
        'caption',
        'heading',
        'description',
        'button',
    ];

    public function render()
    {        
        $this->rendered = true;

        return view('widgets/add-account-teaser', [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'colorscheme' => 'bg-light',
            'caption' => '{"en":"Sign Up Today"}',
            'heading' => '{"en":"Create Your Free Account!"}',
            'description' => '{"en":"It takes just a few minutes to add an account and start submitting listings into the directory. Valid email address is required. You\'ll also need to create a password and agree to terms and conditions."}',
            'button' => '{"en":"Get Started"}',
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.addaccountteaser.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.addaccountteaser.form.label.white'),
                'bg-light' => __('widget.addaccountteaser.form.label.light'),
             ]])
            ->add('caption', 'translatable', ['label' => __('widget.addaccountteaser.form.label.caption')])
            ->add('heading', 'translatable', ['label' => __('widget.addaccountteaser.form.label.heading')])
            ->add('description', 'translatable', ['label' => __('widget.addaccountteaser.form.label.description')])
            ->add('button', 'translatable', ['label' => __('widget.addaccountteaser.form.label.button')]);
    }

}
