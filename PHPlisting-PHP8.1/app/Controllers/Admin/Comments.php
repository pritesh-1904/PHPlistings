<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Comments
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

        if (null === request()->get->get('review_id') || null === $review = \App\Models\Review::find(request()->get->review_id)) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.comments.title.index', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        if (null !== request()->post->get('action') && null !== request()->post->get('id')) {
            switch (request()->post->action) {
                case 'approve':
                    $comments = \App\Models\Comment::whereIn('id', (array) request()->post->get('id'))->whereNull('active')->get();

                    foreach ($comments as $comment) {
                        $comment->approve()->save();
                    }

                    $alert = view('flash/success', ['message' => __('admin.comments.alert.approve.success', ['singular' => $type->name_singular, 'plural' => $type->name_plural])]);
                    break;
                case 'delete':
                    $comments = \App\Models\Comment::whereIn('id', (array) request()->post->get('id'))->get();

                    foreach ($comments as $comment) {
                        $comment->delete();
                    }

                    $alert = view('flash/success', ['message' => __('admin.comments.alert.remove.success', ['singular' => $type->name_singular, 'plural' => $type->name_plural])]);
                    break;
            }
        }

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'desc';
        }

        $comments = \App\Models\Comment::search()
            ->where('review_id', $review->id)
            ->with('user')
            ->paginate();

        return response(layout()->content(
            view('admin/comments/index', [
                'type' => $type,
                'review' => $review,
                'comments' => $this->getTable($comments, $type),
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

        layout()->setTitle(__('admin.comments.title.approve', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        if (null !== request()->post->get('action') && null !== request()->post->get('id')) {
            switch (request()->post->action) {
                case 'approve':
                    $comments = \App\Models\Comment::whereIn('id', (array) request()->post->get('id'))->whereNull('active')->get();

                    foreach ($comments as $comment) {
                        $comment->approve()->save();
                    }

                    $alert = view('flash/success', ['message' => __('admin.comments.alert.approve.success', ['singular' => $type->name_singular, 'plural' => $type->name_plural])]);
                    break;
                case 'delete':
                    $comments = \App\Models\Comment::whereIn('id', (array) request()->post->get('id'))->get();

                    foreach ($comments as $comment) {
                        $comment->delete();
                    }

                    $alert = view('flash/success', ['message' => __('admin.comments.alert.remove.success', ['singular' => $type->name_singular, 'plural' => $type->name_plural])]);
                    break;
            }
        }

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'desc';
        }

        $comments = \App\Models\Comment::search()
            ->where('type_id', $type->id)
            ->whereNull('active')
            ->with(['review', 'user'])
            ->paginate();

        return response(layout()->content(
            view('admin/comments/approve', [
                'type' => $type,
                'comments' => $this->getTable($comments, $type),
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

        if (null === request()->get->get('review_id') || null === $review = \App\Models\Review::find(request()->get->review_id)) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.comments.title.create', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $comment = new \App\Models\Comment();
        $comment->type_id = $type->id;
        $comment->review_id = $review->id;

        $form = $this->getForm($comment, $type)
            ->add('submit', 'submit', ['label' => __('admin.comments.form.label.submit')])
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                $comment->active = $input->get('active');
                $comment->user_id = $input->get('user_id');
                $comment->added_datetime = date('Y-m-d H:i:s');
                $comment->save();

                if (null !== $type->get('active') && null !== $comment->get('active')) {
                    $recipient = ($review->user->id == $comment->user_id) ? $review->listing->user : $review->user;

                    (new \App\Repositories\EmailQueue())->push(
                        'user_comment_created',
                        $recipient->id,
                        [
                            'sender_id' => $comment->user->id,
                            'sender_first_name' => $comment->user->first_name,
                            'sender_last_name' => $comment->user->last_name,
                            'sender_email' => $comment->user->email,

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

                            'link' => route('account/review/' . $review->id),
                        ],
                        [$recipient->email => $recipient->getName()],
                        [config()->email->from_email => config()->email->from_name]
                    );
                }

                return redirect(adminRoute($type->slug . '-comments', ['review_id' => $review->id]))
                    ->with('success', view('flash/success', ['message' => __('admin.comments.alert.create.success')]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/comments/create', [
                'type' => $type,
                'form' => $form,
                'review' => $review,
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

        if (null === request()->get->get('review_id') || null === $review = \App\Models\Review::find(request()->get->review_id)) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $comment = \App\Models\Comment::where('id', $params['id'])->where('type_id', $type->id)->where('review_id', $review->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.comments.title.update', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $form = $this->getForm($comment, $type)
            ->add('submit', 'submit', ['label' => __('admin.comments.form.label.update')])
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                $comment->user_id = $input->get('user_id');

                if ($comment->active != $input->active) {
                    if (null !== $input->active) {
                        $comment->approve();
                    } else {
                        $comment->disapprove();
                    }
                }

                $comment->updated_datetime = date('Y-m-d H:i:s');
                $comment->save();

                return redirect(adminRoute($type->slug . '-comments', ['review_id' => $review->id]))
                    ->with('success', view('flash/success', ['message' => __('admin.comments.alert.update.success')]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/comments/update', [
                'type' => $type,
                'form' => $form,
                'review' => $review,
                'comment' => $comment,
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

        if (null === request()->get->get('review_id') || null === $review = \App\Models\Review::find(request()->get->review_id)) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $comment = \App\Models\Comment::where('id', $params['id'])->where('type_id', $type->id)->where('review_id', $review->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $comment->delete();

        return redirect(adminRoute($type->slug . '-comments', ['review_id' => $review->id]))
            ->with('success', view('flash/success', ['message' => __('admin.comments.alert.remove.success')]));
    }

    private function getTable($comments, $type)
    {
        return dataTable($comments)
            ->addColumns([
                'from' => [__('admin.comments.datatable.label.from'), function ($comment) {
                    return $comment->user->getName();
                }],
                'added_datetime' => [__('admin.comments.datatable.label.added_datetime'), function ($comment) {
                    return locale()->formatDatetime($comment->added_datetime, auth()->user()->timezone);
                }],
                'active' => [__('admin.comments.datatable.label.approved'), function ($comment) {
                    return view('misc/ajax-switch', [
                        'table' => 'comments',
                        'column' => 'active',
                        'id' => $comment->id,
                        'value' => $comment->active
                    ]);
                }],                        
            ])
            ->orderColumns([
                'added_datetime',
            ])
            ->addActions([
                'edit' => [__('admin.comments.datatable.action.edit'), function ($comment) use ($type) {
                    return adminRoute($type->slug . '-comments/update/' . $comment->id, ['review_id' => $comment->review_id]);
                }],
                'delete' => [__('admin.comments.datatable.action.delete'), function ($comment) use ($type) {
                    return adminRoute($type->slug . '-comments/delete/' . $comment->id, ['review_id' => $comment->review_id]);
                }],
            ])
            ->addBulkActions([
                'approve' => __('admin.comments.datatable.bulkaction.approve'),
                'delete' => __('admin.comments.datatable.bulkaction.delete'),
            ]);
    }

    private function getForm($comment, $type)
    {
        $users = [
            $comment->review->user->id => $comment->review->user->getNameWithId(),
            $comment->review->listing->user->id => $comment->review->listing->user->getNameWithId(),
        ];

        return form($comment)
            ->add('active', 'toggle', ['label' => __('admin.comments.form.label.approved'), 'value' => ((null !== $type->approvable_comments) ? null : '1')])
            ->add('user_id', 'select', ['label' => __('admin.comments.form.label.from'), 'options' => $users, 'constraints' => 'required'])
            ->add('description', 'textarea', ['label' => __('admin.comments.form.label.message'), 'constraints' => 'required']);
    }

}
