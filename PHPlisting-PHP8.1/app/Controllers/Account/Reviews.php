<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Account;

class Reviews
    extends \App\Controllers\Account\BaseController
{

    public function actionIndex($params)
    {
        if (null === $page = \App\Models\Page::where('slug', 'account/reviews')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ((null !== config()->general->maintenance || null === $page->active) && false === auth()->check(['admin_login', 'admin_appearance'])) {
            return redirect(route('maintenance'), 302);
        }

        layout()
            ->setTitle($page->title)
            ->setMeta('title', $page->meta_title)
            ->setMeta('keywords', $page->meta_keywords)
            ->setMeta('description', $page->meta_description);

        if (null === request()->get->get('sort')) {
            request()->get->put('sort', 'id');
            request()->get->put('sort_direction', 'desc');
        }

        $reviews = \App\Models\Review::search()
            ->whereHas('type', function ($query) {
                $query->whereNull('deleted');

                if (false === auth()->check('admin_login')) {
                    $query->whereNotNull('active');
                }
            })
            ->where('user_id', auth()->user()->id)
            ->with('listing')
            ->paginate();

        $table = dataTable($reviews)
            ->addColumns([
                'status' => [__('review.datatable.label.status'), function ($review) {
                    return view('misc/status', [
                        'type' => 'review',
                        'status' => $review->active,
                    ]);
                }],
                'title' => [__('review.datatable.label.title')],
                'listing' => [__('review.datatable.label.listing'), function ($review) {
                    return e($review->listing->title);
                }],
                'rating' => [__('review.datatable.label.rating'), function ($review) {
                    return '<p class="m-0 mb-3 text-warning text-nowrap display-11">' . $review->getOutputableValue('_rating') . '</p>';
                }],
                'added_datetime' => [__('review.datatable.label.added_datetime'), function ($review) {
                    return locale()->formatDatetimeDiff($review->added_datetime);
                }],
            ])
            ->orderColumns([
                'title',
                'added_datetime',
            ])
            ->addActions([
                'view' => [__('review.datatable.action.view'), function ($review) {
                    return route('account/reviews/' . $review->id);
                }],
            ]);

        $data = collect([
            'page' => $page,
            'html' => view('account/reviews/index', [
                'reviews' => $table,
            ]),
        ]);

        $response = $page->render($data);

        if ($response instanceof \App\Src\Http\RedirectResponse) {
            return $response;
        }

        return response(
            layout()->content($response)
        );
    }

    public function actionView($params)
    {
        if (null === $page = \App\Models\Page::where('slug', 'account/reviews/view')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ((null !== config()->general->maintenance || null === $page->active) && false === auth()->check(['admin_login', 'admin_appearance'])) {
            return redirect(route('maintenance'), 302);
        }

        layout()
            ->setTitle($page->title)
            ->setMeta('title', $page->meta_title)
            ->setMeta('keywords', $page->meta_keywords)
            ->setMeta('description', $page->meta_description);

        $review = \App\Models\Review::find($params['id']);

        if (null === $review || ($review->user_id != auth()->user()->id && $review->listing->user_id != auth()->user()->id)) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $comments = $review->comments()
            ->where(function ($query) {
                $query
                    ->whereNotNull('active')
                    ->orWhere('user_id', auth()->user()->id);
            })
            ->orderBy('id', 'desc')->with('user')->paginate();

        $comment = new \App\Models\Comment();

        $form = form($comment)
            ->add('description', 'textarea', ['label' => __('review.comment.form.label.message'), 'constraints' => 'required|maxlength:1000'])
            ->add('submit', 'submit', ['label' => __('review.comment.form.label.submit')])
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                if (null === $review->type->approvable_comments) {
                    $comment->active = 1;
                }

                $recipient = ($review->user_id == auth()->user()->id) ? $review->listing->user : $review->user;

                $emailData =                     [
                    'sender_id' => auth()->user()->id,
                    'sender_first_name' => auth()->user()->first_name,
                    'sender_last_name' => auth()->user()->last_name,
                    'sender_email' => auth()->user()->email,

                    'recipient_id' => $recipient->id,
                    'recipient_first_name' => $recipient->first_name,
                    'recipient_last_name' => $recipient->last_name,
                    'recipient_email' => $recipient->email,

                    'listing_id' => $review->listing->id,
                    'listing_title' => $review->listing->title,
                    'listing_type_singular' => $review->type->name_singular,
                    'listing_type_plural' => $review->type->name_plural,

                    'review_id' => $review->id,
                    'review_title' => $review->title,
                    'review_description' => $review->description,

                    'comment_id' => $comment->id,
                    'comment_description' => $comment->description,

                    'link' => adminRoute($review->type->slug . '-comments/approve'),
                ];

                (new \App\Repositories\EmailQueue())->push(
                    (null !== $comment->get('active') ? 'admin_comment_created' : 'admin_comment_created_approve'),
                    null,
                    $emailData,
                    [config()->email->from_email => config()->email->from_name],
                    [config()->email->from_email => config()->email->from_name]
                );

                if (null !== $comment->get('active')) {
                    $emailData['link'] = route('account/reviews/' . $review->id);
                    
                    (new \App\Repositories\EmailQueue())->push(
                        'user_comment_created',
                        $recipient->id,
                        $emailData,
                        [$recipient->email => $recipient->getName()],
                        [config()->email->from_email => config()->email->from_name]
                    );
                }
                    
                $comment->user_id = auth()->user()->id;
                $comment->type_id = $review->type_id;
                $comment->added_datetime = date("Y-m-d H:i:s");
                $review->comments()->save($comment);

                return redirect(route('account/reviews/' . $review->id, session()->get('account/reviews/' . $review->id)))
                    ->with('error', view('flash/success', ['message' => [
                        __(($comment->active == 1 ? 'review.comment.alert.create.success' : 'review.comment.alert.create_with_moderation.success'))
                    ]]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        $data = collect([
            'page' => $page,
            'html' => view('account/reviews/view', [
                'form' => $form,
                'alert' => $alert ?? null,
                'review' => $review,
                'comments' => $comments,
            ]),
        ]);

        $response = $page->render($data);

        if ($response instanceof \App\Src\Http\RedirectResponse) {
            return $response;
        }

        return response(
            layout()->content($response)
        );
    }

}
