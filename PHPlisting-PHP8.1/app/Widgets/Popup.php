<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Popup
    extends \App\Src\Widget\BaseWidget
{
    protected $translatable = [
        'title',
    ];

    public function isMultiInstance()
    {
        return true;
    }

    public function render()
    {        
        $this->rendered = true;

        return view('widgets/popup', [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
            'id' => $this->getId(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'closable' => 1,
            'recurring' => 1,
            'delay' => 3,
            'size' => 'medium',
            'title' => '{"en":"Notification"}',
            'description' => '&lt;p style=&quot;text-align: center;&quot;&gt;Welcome to our website!&lt;/p&gt;',
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('closable', 'toggle', ['label' => __('widget.popup.form.label.closable')])
            ->add('recurring', 'toggle', ['label' => __('widget.popup.form.label.recurring')])
            ->add('delay', 'number', ['label' => __('widget.popup.form.label.delay')])
            ->add('size', 'select', ['label' => __('widget.popup.form.label.size'), 'options' => 
                [
                    'small' => __('widget.popup.form.label.size.small'),
                    'medium' => __('widget.popup.form.label.size.medium'),
                    'large' => __('widget.popup.form.label.size.large'),
                ]
            ])
            ->add('title', 'translatable', ['label' => __('widget.popup.form.label.title')])
            ->add('description', 'htmltextarea', ['label' => __('widget.popup.form.label.description'), 'config' => 'advanced']);
    }

}
