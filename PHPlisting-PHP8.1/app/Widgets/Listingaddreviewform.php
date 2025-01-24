<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Listingaddreviewform
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

        if (null === $this->getData()->listing->get('_reviews')) {
            return null;
        }

        $widgets = $this->getWidgetizer()->all();

        foreach ($widgets as $widget) {
            if ($widget->slug == 'listingaddreviewform' && $widget->getWidgetObject()->isRendered()) {
                return null;
            }
        }

        if (false === auth()->check() || auth()->user()->id == $this->getData()->listing->user_id) {
            if (false !== auth()->check() && auth()->user()->id == $this->getData()->listing->user_id) {
                $alert = view('flash/error', ['message' => __('review.alert.sender_invalid')]);
            } else {
                $alert = view('flash/error', ['message' => __('review.alert.login_required', ['url' => route('account/login')])]);
            }

            return view('widgets/listing-add-review-form', [
                'form' => null,
                'alert' => $alert ?? null,
                'settings' => $this->getSettings(),
                'data' => $this->getData(),
            ]);
        }

        $review = new \App\Models\Review();
        $review->type_id = $this->getData()->listing->type_id;
        $review->setRelation('listing', $this->getData()->listing);

        $form = form($review)
            ->add('rating', 'rating', ['label' => __('review.form.label.rating'), 'value' => 1, 'constraints' => 'required|min:1']);

        $ratings = $this->getData()->listing
            ->type
            ->ratings()
            ->orderBy('weight')
            ->get();

        foreach ($ratings as $rating) {
            $form->add('rating_' . $rating->id, 'rating', ['label' => $rating->name, 'value' => 0, 'constraints' => 'required']);
        }

        $form
            ->add('submit_review', 'submit', ['label' => __('review.form.label.submit')])
            ->handleRequest('submit_review');

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $input = $form->getValues();

                if (null === $this->getData()->listing->type->approvable_reviews) {
                    $review->active = 1;
                }

                $review->user_id = auth()->user()->id;
                $review->added_datetime = date("Y-m-d H:i:s");

                foreach ($ratings as $rating) {
                    $review->put('rating_' . $rating->id, $input->get('rating_' . $rating->id));
                }

                $review->listing_id = $this->getData()->listing->id;

                $review->saveWithData($input);

                (new \App\Repositories\EmailQueue())->push(
                    (null !== $review->get('active') ? 'admin_review_created' : 'admin_review_created_approve'),
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

                        'review_id' => $review->id,
                        'review_title' => $review->title,
                        'review_description' => $review->description,

                        'link' => adminRoute($this->getData()->listing->type->slug . '-reviews/approve'),
                    ],
                    [config()->email->from_email => config()->email->from_name],
                    [config()->email->from_email => config()->email->from_name]
                );

                if (null !== $review->get('active')) {
                    (new \App\Repositories\EmailQueue())->push(
                        'user_review_created',
                        $this->getData()->listing->user->id,
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

                            'review_id' => $review->id,
                            'review_title' => $review->title,
                            'review_description' => $review->description,

                            'link' => route('account/reviews/' . $review->id),
                        ],
                        [$this->getData()->listing->user->email => $this->getData()->listing->user->getName()],
                        [config()->email->from_email => config()->email->from_name]
                    );
                }

                if (null === $this->getData()->listing->type->approvable_reviews) {
                    $this->getData()->listing->recountAvgRating();

                    return redirect(route(getRoute()))
                        ->with('success_review', view('flash/success', ['message' => __('review.form.alert.create.success')]));
                } else {
                    return redirect(route(getRoute()))
                        ->with('success_review', view('flash/success', ['message' => __('review.form.alert.create.success_with_moderation')]));
                }
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        $this->rendered = true;

        return view('widgets/listing-add-review-form', [
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
            'heading' => '{"en":"Leave Feedback"}',
            'description' => '{"en":"Add Review"}',
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.listingaddreviewform.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.listingaddreviewform.form.label.white'),
                'bg-light' => __('widget.listingaddreviewform.form.label.light'),
             ]])
            ->add('heading', 'translatable', ['label' => __('widget.listingaddreviewform.form.label.heading')])
            ->add('description', 'translatable', ['label' => __('widget.listingaddreviewform.form.label.description')]);
    }

}
