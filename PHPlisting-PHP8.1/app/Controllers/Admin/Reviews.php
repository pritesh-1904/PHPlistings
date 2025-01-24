<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Reviews
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (false === auth()->check(['admin_content', 'admin_reviews'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.reviews.title.index', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        if (null !== request()->post->get('action') && null !== request()->post->get('id')) {
            switch (request()->post->action) {
                case 'approve':
                    $reviews = \App\Models\Review::whereIn('id', (array) request()->post->get('id'))->whereNull('active')->get();

                    foreach ($reviews as $review) {
                        $review->approve()->save();
                        $review->listing->recountAvgRating();
                    }

                    $alert = view('flash/success', ['message' => __('admin.reviews.alert.approve.success', ['singular' => $type->name_singular, 'plural' => $type->name_plural])]);
                    break;
                case 'delete':
                    $reviews = \App\Models\Review::whereIn('id', (array) request()->post->get('id'))->with('listing')->get();

                    foreach ($reviews as $review) {
                        $listing = $review->listing;

                        $review->delete();

                        $listing->recountAvgRating();
                    }

                    $alert = view('flash/success', ['message' => __('admin.reviews.alert.remove.success', ['singular' => $type->name_singular, 'plural' => $type->name_plural])]);
                    break;
            }
        }

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'asc';
        }

        $reviews = \App\Models\Review::search(null, [], 'admin/' . $type->slug . '-reviews')
            ->where('type_id', $type->id)
            ->with([
                'comments',
                'listing',
                'user',
            ])
            ->paginate();

        $form = form()
            ->setMethod('get')
            ->setTemplate('form/inline')
            ->add('listing_id', 'listing', [
                'placeholder' => __('admin.reviews.searchform.placeholder.listing'),
                'type' => $type->id,
                'weight' => 10,
            ])
            ->add('submit', 'submit', [
                'label' => __('admin.reviews.searchform.label.submit')
            ])
            ->forceRequest();

        return response(layout()->content(
            view('admin/reviews/index', [
                'type' => $type,
                'form' => $form,
                'listing' => (null !== request()->get->get('listing_id') ? \App\Models\Listing::find(request()->get->get('listing_id')) : null),
                'reviews' => $this->getTable($reviews, $type),
                'alert' => $alert ?? null
            ])
        )); 
    }

    public function actionApprove($params)
    {
        if (false === auth()->check(['admin_content', 'admin_reviews'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.reviews.title.approve', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        if (null !== request()->post->get('action') && null !== request()->post->get('id')) {
            switch (request()->post->action) {
                case 'approve':
                    $reviews = \App\Models\Review::whereIn('id', (array) request()->post->get('id'))->whereNull('active')->get();

                    foreach ($reviews as $review) {
                        $review->approve()->save();
                        $review->listing->recountAvgRating();
                    }

                    $alert = view('flash/success', ['message' => __('admin.reviews.alert.approve.success', ['singular' => $type->name_singular, 'plural' => $type->name_plural])]);
                    break;
                case 'delete':
                    $reviews = \App\Models\Review::whereIn('id', (array) request()->post->get('id'))->with('listing')->get();

                    foreach ($reviews as $review) {
                        $listing = $review->listing;

                        $review->delete();

                        $listing->recountAvgRating();
                    }

                    $alert = view('flash/success', ['message' => __('admin.reviews.alert.remove.success', ['singular' => $type->name_singular, 'plural' => $type->name_plural])]);
                    break;
            }
        }

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'desc';
        }

        $reviews = \App\Models\Review::search(null, [], 'admin/' . $type->slug . '-reviews/approve')
            ->where('type_id', $type->id)
            ->whereNull('active')
            ->with([
                'comments',
                'listing',
                'user',
            ])
            ->paginate();

        $form = form()
            ->setMethod('get')
            ->setTemplate('form/inline')
            ->add('listing_id', 'listing', [
                'placeholder' => __('admin.reviews.searchform.placeholder.listing'),
                'type' => $type->id,
                'weight' => 10,
            ])
            ->add('submit', 'submit', [
                'label' => __('admin.reviews.searchform.label.submit')
            ])
            ->forceRequest();

        return response(layout()->content(
            view('admin/reviews/approve', [
                'type' => $type,
                'form' => $form,
                'listing' => (null !== request()->get->get('listing_id') ? \App\Models\Listing::find(request()->get->get('listing_id')) : null),
                'reviews' => $this->getTable($reviews, $type),
                'alert' => $alert ?? null
            ])
        )); 
    }

    public function actionCreate($params)
    {
        if (false === auth()->check(['admin_content', 'admin_reviews'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.reviews.title.create', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $ratings = $type
            ->ratings()
            ->orderBy('weight')
            ->get();

        $review = new \App\Models\Review();
        $review->type_id = $type->id;

        $form = $this->getForm($type, $ratings)
            ->bindModel($review)
            ->add('submit', 'submit', ['label' => __('admin.reviews.form.label.submit')])
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                $review->active = $input->get('active');
                $review->user_id = $input->get('user_id');
                $review->listing_id = $input->get('listing_id');
                $review->added_datetime = date('Y-m-d H:i:s');

                foreach ($ratings as $rating) {
                    $review->put('rating_' . $rating->id, $input->get('rating_' . $rating->id));
                }

                $review->saveWithData($input);

                if (null !== $review->get('active')) {
                    $review->listing->recountAvgRating();

                    if (null !== $type->get('active')) {
                        (new \App\Repositories\EmailQueue())->push(
                            'user_review_created',
                            $review->listing->user->id,
                            [
                                'sender_id' => $review->user->id,
                                'sender_first_name' => $review->user->first_name,
                                'sender_last_name' => $review->user->last_name,
                                'sender_email' => $review->user->email,

                                'recipient_id' => $review->listing->user->id,
                                'recipient_first_name' => $review->listing->user->first_name,
                                'recipient_last_name' => $review->listing->user->last_name,
                                'recipient_email' => $review->listing->user->email,

                                'listing_id' => $review->listing->id,
                                'listing_title' => $review->listing->title,
                                'listing_type_singular' => $type->name_singular,
                                'listing_type_plural' => $type->name_plural,

                                'review_id' => $review->id,
                                'review_title' => $review->title,
                                'review_description' => $review->description,

                                'link' => route('account/reviews/' . $review->id),
                            ],
                            [$review->listing->user->email => $review->listing->user->getName()],
                            [config()->email->from_email => config()->email->from_name]
                        );
                    }
                }

                return redirect(adminRoute($type->slug . '-reviews', session()->get('admin/' . $type->slug . '-reviews')))
                    ->with('success', view('flash/success', ['message' => __('admin.reviews.alert.create.success')]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/reviews/create', [
                'type' => $type,
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionUpdate($params)
    {
        if (false === auth()->check(['admin_content', 'admin_reviews'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $review = \App\Models\Review::where('id', $params['id'])->where('type_id', $type->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.reviews.title.update', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $ratings = $type
            ->ratings()
            ->orderBy('weight')
            ->get();

        $form = $this->getForm($type, $ratings)
            ->bindModel($review)
            ->add('submit', 'submit', ['label' => __('admin.reviews.form.label.update')])
            ->setValues($review->data->pluck('value', 'field_name')->all())
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                if ($review->user_id != $input->get('user_id')) {
                    $review->comments()->where('user_id', $review->user_id)->update(['user_id' => $input->get('user_id')]);
                }
                
                if ($review->listing_id != $input->get('listing_id')) {
                    if (null !== $oldListing = \App\Models\Listing::find($review->get('listing_id'))) {
                        if (null !== $listing = \App\Models\Listing::find($input->get('listing_id'))) {
                            $review->comments()->where('user_id', $oldListing->user_id)->update(['user_id' => $listing->get('user_id')]);
                        }
                    }
                }
                
                $review->user_id = $input->get('user_id');
                $review->listing_id = $input->get('listing_id');

                if ($review->active != $input->active) {
                    if (null !== $input->active) {
                        $review->approve();
                    } else {
                        $review->disapprove();
                    }
                }

                $review->updated_datetime = date('Y-m-d H:i:s');

                foreach ($ratings as $rating) {
                    $review->put('rating_' . $rating->id, $input->get('rating_' . $rating->id));
                }
                
                $review->saveWithData($input);

                $review->listing->recountAvgRating();

                if (isset($listing)) {
                    $listing->recountAvgRating();
                }

                return redirect(adminRoute($type->slug . '-reviews', session()->get('admin/' . $type->slug . '-reviews')))
                    ->with('success', view('flash/success', ['message' => __('admin.reviews.alert.update.success')]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/reviews/update', [
                'type' => $type,
                'form' => $form,
                'review' => $review,
                'alert' => $alert ?? null
            ])
        )); 
    }

    public function actionDelete($params)
    {
        if (false === auth()->check(['admin_content', 'admin_reviews'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $review = \App\Models\Review::where('id', $params['id'])->where('type_id', $type->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $review->delete();

        if (null !== $review->active) {
            $review->listing->recountAvgRating();
        }

        return redirect(adminRoute($type->slug . '-reviews', session()->get('admin/' . $type->slug . '-reviews')))
            ->with('success', view('flash/success', ['message' => __('admin.reviews.alert.remove.success')]));
    }

    private function getTable($reviews, $type)
    {
        return dataTable($reviews)
            ->addColumns([
                'title' => [__('admin.reviews.datatable.label.subject')],
                'from' => [__('admin.reviews.datatable.label.from'), function ($review) {
                    return $review->user->getName();
                }],
                'rating' => [__('admin.reviews.datatable.label.rating'), function ($review) {
                    return '<p class="m-0 mb-3 text-warning text-nowrap display-11">' . $review->getOutputableValue('_rating') . '</p>';
                }],
                'added_datetime' => [__('admin.reviews.datatable.label.added_datetime'), function ($review) {
                    return locale()->formatDatetime($review->added_datetime, auth()->user()->timezone);
                }],
                'active' => [__('admin.reviews.datatable.label.approved'), function ($review) {
                    return view('misc/ajax-switch', [
                        'table' => 'reviews',
                        'column' => 'active',
                        'id' => $review->id,
                        'value' => $review->active
                    ]);
                }],                        
            ])
            ->orderColumns([
                'added_datetime',
            ])
            ->addActions([
                'edit' => [__('admin.reviews.datatable.action.edit'), function ($review) use ($type) {
                    return adminRoute($type->slug . '-reviews/update/' . $review->id);
                }],
                'comments' => [
                    function ($review) {
                        return __('admin.reviews.datatable.action.comments', ['count' => '<span class="badge badge-secondary">' . $review->comments->count() . '</span>']);
                    },
                    function ($review) use ($type) {
                        return adminRoute($type->slug . '-comments', ['review_id' => $review->id]);
                    },
                ],
                'delete' => [__('admin.reviews.datatable.action.delete'), function ($review) use ($type) {
                    return adminRoute($type->slug . '-reviews/delete/' . $review->id);
                }],
            ])
            ->addBulkActions([
                'approve' => __('admin.reviews.datatable.bulkaction.approve'),
                'delete' => __('admin.reviews.datatable.bulkaction.delete'),
            ]);
    }

    private function getForm($type, $ratings)
    {
        $form = form()
            ->add('active', 'toggle', ['label' => __('admin.reviews.form.label.approved'), 'value' => (($type->approvable_reviews == '1') ? null : '1')])
            ->add('user_id', 'user', ['label' => __('admin.reviews.form.label.from'), 'constraints' => 'required'])
            ->add('listing_id', 'listing', ['label' => __('admin.reviews.form.label.listing'), 'constraints' => 'required|listing:' . $type->id, 'value' => (null !== request()->get->get('listing_id') ? request()->get->get('listing_id') : null), 'type' => $type->id])
            ->add('rating', 'rating', ['label' => __('admin.reviews.form.label.rating'), 'value' => 1, 'constraints' => 'required|min:1']);

        foreach ($ratings as $rating) {
            $form->add('rating_' . $rating->id, 'rating', ['label' => $rating->name, 'value' => 0, 'constraints' => 'required']);
        }

        return $form;
    }

}
