<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Contactform
    extends \App\Src\Widget\BaseWidget
{

    protected $translatable = [
        'heading',
        'description',
    ];

    public function isMultiInstance()
    {
        return true;
    }

    public function render()
    {
        $widgets = $this->getWidgetizer()->all();

        foreach ($widgets as $widget) {
            if ($widget->slug == 'contactform' && $widget->getWidgetObject()->isRendered()) {
                return null;
            }
        }
        
        $this->rendered = true;

        $form = form()
            ->import(\App\Models\WidgetFieldGroup::find($this->getSettings()->form_group)->fields()->orderBy('weight')->get())
            ->add('submit_contact', 'submit', ['label' => __('contact.form.label.submit')])
            ->handleRequest('submit_contact');

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                (new \App\Repositories\EmailQueue())->push(
                    $this->getSettings()->template,
                    null,
                    $form->getOutputableValues()->all(),
                    [config()->email->from_email => config()->email->from_name],
                    [config()->email->from_email => config()->email->from_name]
                );

                return redirect(route(getRoute()))
                    ->with('success_contact', view('flash/success', ['message' => __('contact.form.alert.create.success')]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return view('widgets/contact-form', [
            'form' => $form,
            'alert' => $alert ?? null,
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'colorscheme' => 'bg-white',
            'heading' => '{"en":"Contact Us"}',
            'description' => '{"en":"What can we help you with?"}',
            'form_group' => 1,
            'template' => 'admin_contact_form_submitted',
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.contactform.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.contactform.form.label.white'),
                'bg-light' => __('widget.contactform.form.label.light'),
             ]])
            ->add('heading', 'translatable', ['label' => __('widget.contactform.form.label.heading')])
            ->add('description', 'translatable', ['label' => __('widget.contactform.form.label.description')])
            ->add('form_group', 'select', ['label' => __('widget.contactform.form.label.form_group'), 'options' => \App\Models\WidgetFieldGroup::all()->pluck('name', 'id')->all(), 'constraints' => 'required'])
            ->add('template', 'select', ['label' => __('widget.contactform.form.label.template'), 'options' => \App\Models\EmailTemplate::whereNotNull('customizable')->orWhere('name', 'admin_contact_form_submitted')->get()->pluck('name', 'name')->all(), 'constraints' => 'required']);
    }

}
