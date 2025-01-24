<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Listingclaimform
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

        if (null !== $this->getData()->listing->claimed) {
            return null;
        }

        $widgets = $this->getWidgetizer()->all();

        foreach ($widgets as $widget) {
            if ($widget->slug == 'listingclaimform' && $widget->getWidgetObject()->isRendered()) {
                return null;
            }
        }

        if (false === auth()->check()) {
            $alert = view('flash/error', ['message' => __('claim.alert.login_required', ['url' => route('account/login')])]);
            
            $this->rendered = true;

            return view('widgets/listing-claim-form', [
                'form' => null,
                'alert' => $alert ?? null,
                'settings' => $this->getSettings(),                             
                'data' => $this->getData(),
            ]);
        }

        $pricings = \App\Models\Pricing::query()
            ->whereNull('hidden')
            ->whereNotNull('claimable')
            ->whereHas('product', function($query) {
                $query
                    ->whereNull('hidden')
                    ->where('type_id', $this->getData()->listing->type_id);
            })
            ->with('product')
            ->get();

        $claim = new \App\Models\Claim();

        $claim->status = 'pending';
        $claim->type_id = $this->getData()->listing->type_id;

        $form = form($claim)
            ->add('listing', 'ro', ['label' => __('claim.form.label.listing'), 'value' => $this->getData()->listing->title])
            ->add('pricing_id', 'select', ['label' => __('claim.form.label.pricing'), 'options' => $pricings->pluck(function ($pricing) { return $pricing->getNameWithProduct(); }, 'id')->all(), 'constraints' => 'required|number'])
            ->add('comment', 'textarea', ['label' => __('claim.form.label.comments'), 'constraints' => 'required'])
            ->add('submit_claim', 'submit', ['label' => __('claim.form.label.submit')])
            ->handleRequest('submit_claim');

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if (false === $pricings->contains('id', $input->pricing_id)) {
                $form->setValidationError('pricing_id', __('claim.form.alert.invalid_pricing'));
            }

            if (auth()->user()->claims()->where('listing_id', $this->getData()->listing->id)->count() > 0) {
                $form->setValidationError('listing', __('claim.form.alert.already_claimed'));
            }

            if ($form->isValid()) {
                $claim->user_id = auth()->user()->id;
                $claim->added_datetime = date("Y-m-d H:i:s");

                $this->getData()->listing->claims()->save($claim);

                (new \App\Repositories\EmailQueue())->push(
                    'admin_listing_claimed',
                    null,
                    [
                        'id' => auth()->user()->id,
                        'first_name' => auth()->user()->first_name,
                        'last_name' => auth()->user()->last_name,
                        'email' => auth()->user()->email,

                        'listing_id' => $this->getData()->listing->id,
                        'listing_title' => $this->getData()->listing->title,
                        'listing_type_singular' => $this->getData()->listing->type->name_singular,
                        'listing_type_plural' => $this->getData()->listing->type->name_plural,

                        'link' => adminRoute($this->getData()->listing->type->slug . '-claims'),
                    ],
                    [config()->email->from_email => config()->email->from_name],
                    [config()->email->from_email => config()->email->from_name]
                );

                return redirect(route(getRoute()))
                    ->with('success_claim', view('flash/success', ['message' => __('claim.form.alert.create.success')]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        $this->rendered = true;

        return view('widgets/listing-claim-form', [
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
            'heading' => '{"en":"Claim Your Listing"}',
            'description' => '{"en":"Request ownership of a business listing."}',
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.listingclaimform.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.listingclaimform.form.label.white'),
                'bg-light' => __('widget.listingclaimform.form.label.light'),
             ]])
            ->add('heading', 'translatable', ['label' => __('widget.listingclaimform.form.label.heading')])
            ->add('description', 'translatable', ['label' => __('widget.listingclaimform.form.label.description')]);
    }

}
