<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Replies
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (false === auth()->check(['admin_content', 'admin_messages'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === request()->get->get('message_id') || null === $message = \App\Models\Message::find(request()->get->message_id)) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.replies.title.index', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        if (null !== request()->post->get('action') && null !== request()->post->get('id')) {
            switch (request()->post->action) {
                case 'approve':
                    $replies = \App\Models\Reply::whereIn('id', (array) request()->post->get('id'))->whereNull('active')->get();

                    foreach ($replies as $reply) {
                        $reply->approve()->save();
                    }

                    $alert = view('flash/success', ['message' => __('admin.replies.alert.approve.success', ['singular' => $type->name_singular, 'plural' => $type->name_plural])]);
                    break;
                case 'delete':
                    $replies = \App\Models\Reply::whereIn('id', (array) request()->post->get('id'))->get();

                    foreach ($replies as $reply) {
                        $reply->delete();
                    }

                    $alert = view('flash/success', ['message' => __('admin.replies.alert.remove.success', ['singular' => $type->name_singular, 'plural' => $type->name_plural])]);
                    break;
            }
        }

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'desc';
        }

        $replies = \App\Models\Reply::search()
            ->where('message_id', $message->id)
            ->with('user')
            ->paginate();

        return response(layout()->content(
            view('admin/replies/index', [
                'type' => $type,
                'message' => $message,
                'replies' => $this->getTable($replies, $type),
                'alert' => $alert ?? null
            ])
        )); 
    }

    public function actionApprove($params)
    {
        if (false === auth()->check(['admin_content', 'admin_messages'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.replies.title.approve', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        if (null !== request()->post->get('action') && null !== request()->post->get('id')) {
            switch (request()->post->action) {
                case 'approve':
                    $replies = \App\Models\Reply::whereIn('id', (array) request()->post->get('id'))->whereNull('active')->get();

                    foreach ($replies as $reply) {
                        $reply->approve()->save();
                    }

                    $alert = view('flash/success', ['message' => __('admin.replies.alert.approve.success', ['singular' => $type->name_singular, 'plural' => $type->name_plural])]);
                    break;
                case 'delete':
                    $replies = \App\Models\Reply::whereIn('id', (array) request()->post->get('id'))->get();

                    foreach ($replies as $reply) {
                        $reply->delete();
                    }

                    $alert = view('flash/success', ['message' => __('admin.replies.alert.remove.success', ['singular' => $type->name_singular, 'plural' => $type->name_plural])]);
                    break;
            }
        }

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'desc';
        }

        $replies = \App\Models\Reply::search()
            ->where('type_id', $type->id)
            ->whereNull('active')
            ->with(['message', 'user'])
            ->paginate();

        return response(layout()->content(
            view('admin/replies/approve', [
                'type' => $type,
                'replies' => $this->getTable($replies, $type),
                'alert' => $alert ?? null
            ])
        )); 
    }

    public function actionCreate($params)
    {
        if (false === auth()->check(['admin_content', 'admin_messages'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === request()->get->get('message_id') || null === $message = \App\Models\Message::find(request()->get->message_id)) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.replies.title.create', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $reply = new \App\Models\Reply();
        $reply->type_id = $type->id;
        $reply->message_id = $message->id;

        $form = $this->getForm($reply, $type)
            ->add('submit', 'submit', ['label' => __('admin.replies.form.label.submit')])
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                $reply->active = $input->get('active');
                $reply->user_id = $input->get('user_id');
                $reply->added_datetime = date('Y-m-d H:i:s');
                $reply->save();

                if (null !== $reply->get('active')) {
                    $recipient = ($message->sender->id == $reply->user_id) ? $reply->user : $message->sender;

                    if (null !== $type->get('active')) {
                        (new \App\Repositories\EmailQueue())->push(
                            'user_reply_created',
                            $recipient->id,
                            [
                                'sender_id' => $reply->user->id,
                                'sender_first_name' => $reply->user->first_name,
                                'sender_last_name' => $reply->user->last_name,
                                'sender_email' => $reply->user->email,

                                'recipient_id' => $recipient->id,
                                'recipient_first_name' => $recipient->first_name,
                                'recipient_last_name' => $recipient->last_name,
                                'recipient_email' => $recipient->email,

                                'listing_id' => $message->listing->id,
                                'listing_title' => $message->listing->title,
                                'listing_type_singular' => $message->type->name_singular,
                                'listing_type_plural' => $message->type->name_plural,

                                'message_id' => $message->id,
                                'message_title' => $message->title,
                                'message_description' => $message->description,

                                'reply_id' => $reply->id,
                                'reply_description' => $reply->description,

                                'link' => route('account/messages/' . $message->id),
                            ],
                            [$recipient->email => $recipient->getName()],
                            [config()->email->from_email => config()->email->from_name]
                        );
                    }
                }

                return redirect(adminRoute($type->slug . '-replies', ['message_id' => $message->id]))
                    ->with('success', view('flash/success', ['message' => __('admin.replies.alert.create.success')]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/replies/create', [
                'type' => $type,
                'form' => $form,
                'message' => $message,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionUpdate($params)
    {
        if (false === auth()->check(['admin_content', 'admin_messages'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === request()->get->get('message_id') || null === $message = \App\Models\Message::find(request()->get->message_id)) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $reply = \App\Models\Reply::where('id', $params['id'])->where('type_id', $type->id)->where('message_id', $message->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.replies.title.update', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $form = $this->getForm($reply, $type)
            ->add('submit', 'submit', ['label' => __('admin.replies.form.label.update')])
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                $reply->user_id = $input->get('user_id');

                if ($reply->active != $input->active) {
                    if (null !== $input->active) {
                        $reply->approve();
                    } else {
                        $reply->disapprove();
                    }
                }

                $reply->updated_datetime = date('Y-m-d H:i:s');
                $reply->save();

                return redirect(adminRoute($type->slug . '-replies', ['message_id' => $message->id]))
                    ->with('success', view('flash/success', ['message' => __('admin.replies.alert.update.success')]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/replies/update', [
                'type' => $type,
                'form' => $form,
                'message' => $message,
                'reply' => $reply,
                'alert' => $alert ?? null
            ])
        )); 
    }

    public function actionDelete($params)
    {
        if (false === auth()->check(['admin_content', 'admin_messages'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === request()->get->get('message_id') || null === $message = \App\Models\Message::find(request()->get->message_id)) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $reply = \App\Models\Reply::where('id', $params['id'])->where('type_id', $type->id)->where('message_id', $message->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $reply->delete();

        return redirect(adminRoute($type->slug . '-replies', ['message_id' => $message->id]))
            ->with('success', view('flash/success', ['message' => __('admin.replies.alert.remove.success')]));
    }

    private function getTable($replies, $type)
    {
        return dataTable($replies)
            ->addColumns([
                'from' => [__('admin.replies.datatable.label.from'), function ($reply) {
                    return $reply->user->getName();
                }],
                'added_datetime' => [__('admin.replies.datatable.label.added_datetime'), function ($reply) {
                    return locale()->formatDatetime($reply->added_datetime, auth()->user()->timezone);
                }],
                'active' => [__('admin.replies.datatable.label.approved'), function ($reply) {
                    return view('misc/ajax-switch', [
                        'table' => 'replies',
                        'column' => 'active',
                        'id' => $reply->id,
                        'value' => $reply->active
                    ]);
                }],                        
            ])
            ->orderColumns([
                'added_datetime',
            ])
            ->addActions([
                'edit' => [__('admin.replies.datatable.action.edit'), function ($reply) use ($type) {
                    return adminRoute($type->slug . '-replies/update/' . $reply->id, ['message_id' => $reply->message_id]);
                }],
                'delete' => [__('admin.replies.datatable.action.delete'), function ($reply) use ($type) {
                    return adminRoute($type->slug . '-replies/delete/' . $reply->id, ['message_id' => $reply->message_id]);
                }],
            ])
            ->addBulkActions([
                'approve' => __('admin.replies.datatable.bulkaction.approve'),
                'delete' => __('admin.replies.datatable.bulkaction.delete'),
            ]);
    }

    private function getForm($reply, $type)
    {
        $users = [
            $reply->message->sender->id => $reply->message->sender->getNameWithId(),
            $reply->message->recipient->id => $reply->message->recipient->getNameWithId(),
        ];

        return form($reply)
            ->add('active', 'toggle', ['label' => __('admin.replies.form.label.approved'), 'value' => ((null !== $type->approvable_replies) ? null : '1')])
            ->add('user_id', 'select', ['label' => __('admin.replies.form.label.from'), 'options' => $users, 'constraints' => 'required'])
            ->add('description', 'textarea', ['label' => __('admin.replies.form.label.message'), 'constraints' => 'required']);
    }

}
