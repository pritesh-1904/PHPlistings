<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Listingsendmessageform
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
        if (null === $this->getData()->get('listing')) {
            return null;
        }

        if (null === $this->getData()->listing->get('_send_message')) {
            return null;
        }

        $widgets = $this->getWidgetizer()->all();

        foreach ($widgets as $widget) {
            if ($widget->slug == 'listingsendmessageform' && $widget->getWidgetObject()->isRendered()) {
                return null;
            }
        }

        if (false === auth()->check() || auth()->user()->id == $this->getData()->listing->user_id) {
            if (false !== auth()->check() && auth()->user()->id == $this->getData()->listing->user_id) {
                $alert = view('flash/error', ['message' => __('message.alert.sender_invalid')]);
            } else {
                $alert = view('flash/error', ['message' => __('message.alert.login_required', ['url' => route('account/login')])]);
            }

            $this->rendered = true;

            return view('widgets/listing-send-message-form', [
                'form' => null,
                'alert' => $alert ?? null,
                'settings' => $this->getSettings(),
                'data' => $this->getData(),
            ]);
        }

        $message = new \App\Models\Message();
        $message->type_id = $this->getData()->listing->type_id;       
        $message->setRelation('listing', $this->getData()->listing);
        
        $form = form($message)
            ->add('submit_message', 'submit', ['label' => __('message.form.label.submit')])
            ->handleRequest('submit_message');

        if ($form->isSubmitted()) {
            $input = $form->getValues();
            
            if ($form->isValid()) {
                if (null === $this->getData()->listing->type->approvable_messages) {
                    $message->active = 1;
                }

                $message->sender_id = auth()->user()->id;
                $message->recipient_id = $this->getData()->listing->user_id;
                $message->added_datetime = date("Y-m-d H:i:s");
                $message->listing_id = $this->getData()->listing->id;
                $message->saveWithData($input);

                (new \App\Repositories\EmailQueue())->push(
                    (null !== $message->get('active') ? 'admin_message_created' : 'admin_message_created_approve'),
                    null,
                    [
                        'sender_id' => auth()->user()->id,
                        'sender_first_name' => auth()->user()->first_name,
                        'sender_last_name' => auth()->user()->last_name,
                        'sender_email' => auth()->user()->email,

                        'recipient_id' => $this->getData()->listing->user->id,
                        'recipient_first_name' => $this->getData()->listing->user->first_name,
                        'recipient_last_name' => $this->getData()->listing->user->last_name,
                        'recipient_email' => $this->getData()->listing->user->email,

                        'listing_id' => $this->getData()->listing->id,
                        'listing_title' => $this->getData()->listing->title,
                        'listing_type_singular' => $this->getData()->listing->type->name_singular,
                        'listing_type_plural' => $this->getData()->listing->type->name_plural,

                        'message_id' => $message->id,
                        'message_title' => $message->title,
                        'message_description' => $message->description,

                        'link' => adminRoute($this->getData()->listing->type->slug . '-messages/approve'),
                    ],
                    [config()->email->from_email => config()->email->from_name],
                    [config()->email->from_email => config()->email->from_name]
                );

                if (null !== $message->get('active')) {
                    (new \App\Repositories\EmailQueue())->push(
                        'user_message_created',
                        auth()->user()->id,
                        [
                            'sender_id' => auth()->user()->id,
                            'sender_first_name' => auth()->user()->first_name,
                            'sender_last_name' => auth()->user()->last_name,
                            'sender_email' => auth()->user()->email,

                            'recipient_id' => $this->getData()->listing->user->id,
                            'recipient_first_name' => $this->getData()->listing->user->first_name,
                            'recipient_last_name' => $this->getData()->listing->user->last_name,
                            'recipient_email' => $this->getData()->listing->user->email,

                            'listing_id' => $this->getData()->listing->id,
                            'listing_title' => $this->getData()->listing->title,
                            'listing_type_singular' => $this->getData()->listing->type->name_singular,
                            'listing_type_plural' => $this->getData()->listing->type->name_plural,

                            'message_id' => $message->id,
                            'message_title' => $message->title,
                            'message_description' => $message->description,

                            'link' => route('account/messages/' . $message->id),
                        ],
                        [$this->getData()->listing->user->email => $this->getData()->listing->user->getName()],
                        [config()->email->from_email => config()->email->from_name]
                    );
                }

                return redirect(route(getRoute()))
                    ->with('success_message', view('flash/success', ['message' => (null !== $message->get('active') ? __('message.form.alert.create.success') : __('message.form.alert.create_with_approval.success'))]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        $this->rendered = true;

        return view('widgets/listing-send-message-form', [
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
            'heading' => '{"en":"Send Message"}',
            'description' => '{"en":"Request for information or assistance."}',
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.listingsendmessageform.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.listingsendmessageform.form.label.white'),
                'bg-light' => __('widget.listingsendmessageform.form.label.light'),
             ]])
            ->add('heading', 'translatable', ['label' => __('widget.listingsendmessageform.form.label.heading')])
            ->add('description', 'translatable', ['label' => __('widget.listingsendmessageform.form.label.description')]);
    }

}
