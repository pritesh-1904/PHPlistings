<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Messages
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

        layout()->setTitle(__('admin.messages.title.index', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        if (null !== request()->post->get('action') && null !== request()->post->get('id')) {
            switch (request()->post->action) {
                case 'approve':
                    $messages = \App\Models\Message::whereIn('id', (array) request()->post->get('id'))->whereNull('active')->get();

                    foreach ($messages as $message) {
                        $message->approve()->save();
                    }

                    $alert = view('flash/success', ['message' => __('admin.messages.alert.approve.success', ['singular' => $type->name_singular, 'plural' => $type->name_plural])]);
                    break;
                case 'delete':
                    $messages = \App\Models\Message::whereIn('id', (array) request()->post->get('id'))->get();

                    foreach ($messages as $message) {
                        $message->delete();
                    }

                    $alert = view('flash/success', ['message' => __('admin.messages.alert.remove.success', ['singular' => $type->name_singular, 'plural' => $type->name_plural])]);
                    break;
            }
        }

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'desc';
        }

        $messages = \App\Models\Message::search(null, [], 'admin/' . $type->slug . '-messages')
            ->where('type_id', $type->id)
            ->with([
                'replies',
//                'listing',
                'sender',
            ])
            ->paginate();

        $form = form()
            ->setMethod('get')
            ->setTemplate('form/inline')
            ->add('listing_id', 'listing', [
                'placeholder' => __('admin.messages.searchform.placeholder.listing'),
                'type' => $type->id,
                'weight' => 10,
            ])
            ->add('submit', 'submit', [
                'label' => __('admin.messages.searchform.label.submit')
            ])
            ->forceRequest();

        return response(layout()->content(
            view('admin/messages/index', [
                'type' => $type,
                'form' => $form,
                'listing' => (null !== request()->get->get('listing_id') ? \App\Models\Listing::find(request()->get->get('listing_id')) : null),
                'messages' => $this->getTable($messages, $type),
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

        layout()->setTitle(__('admin.messages.title.approve', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        if (null !== request()->post->get('action') && null !== request()->post->get('id')) {
            switch (request()->post->action) {
                case 'approve':
                    $messages = \App\Models\Message::whereIn('id', (array) request()->post->get('id'))->whereNull('active')->get();

                    foreach ($messages as $message) {
                        $message->approve()->save();
                    }

                    $alert = view('flash/success', ['message' => __('admin.messages.alert.approve.success', ['singular' => $type->name_singular, 'plural' => $type->name_plural])]);
                    break;
                case 'delete':
                    $messages = \App\Models\Message::whereIn('id', (array) request()->post->get('id'))->get();

                    foreach ($messages as $message) {
                        $message->delete();
                    }

                    $alert = view('flash/success', ['message' => __('admin.messages.alert.remove.success', ['singular' => $type->name_singular, 'plural' => $type->name_plural])]);
                    break;
            }
        }

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'desc';
        }

        $messages = \App\Models\Message::search(null, [], 'admin/' . $type->slug . '-messages/approve')
            ->where('type_id', $type->id)
            ->whereNull('active')
            ->with([
                'replies',
                'sender',
            ])
            ->paginate();

        $form = form()
            ->setMethod('get')
            ->setTemplate('form/inline')
            ->add('listing_id', 'listing', [
                'placeholder' => __('admin.messages.searchform.placeholder.listing'),
                'type' => $type->id,
                'weight' => 10,
            ])
            ->add('submit', 'submit', [
                'label' => __('admin.messages.searchform.label.submit')
            ])
            ->forceRequest();

        return response(layout()->content(
            view('admin/messages/approve', [
                'type' => $type,
                'form' => $form,
                'listing' => (null !== request()->get->get('listing_id') ? \App\Models\Listing::find(request()->get->get('listing_id')) : null),
                'messages' => $this->getTable($messages, $type),
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

        layout()->setTitle(__('admin.messages.title.create', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $message = new \App\Models\Message();
        $message->type_id = $type->id;

        $form = form()
            ->add('active', 'toggle', ['label' => __('admin.messages.form.label.approved'), 'value' => (($type->approvable_messages == '1') ? null : '1')])
            ->add('sender_id', 'user', ['label' => __('admin.messages.form.label.from'), 'constraints' => 'required'])
            ->add('listing_id', 'listing', ['label' => __('admin.messages.form.label.listing'), 'constraints' => 'required|listing:' . $type->id, 'value' => (null !== request()->get->get('listing_id') ? request()->get->get('listing_id') : null), 'type' => $type->id])
            ->bindModel($message)
            ->add('submit', 'submit', ['label' => __('admin.messages.form.label.submit')])
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                $message->active = $input->get('active');
                $message->sender_id = $input->get('sender_id');
                $message->listing_id = $input->get('listing_id');
                $message->recipient_id = $message->listing->user_id;
                $message->added_datetime = date('Y-m-d H:i:s');
                $message->saveWithData($input);

                if (null !== $type->get('active') && null !== $message->get('active')) {
                    (new \App\Repositories\EmailQueue())->push(
                        'user_message_created',
                        $message->recipient->id,
                        [
                            'sender_id' => $message->sender->id,
                            'sender_first_name' => $message->sender->first_name,
                            'sender_last_name' => $message->sender->last_name,
                            'sender_email' => $message->sender->email,

                            'recipient_id' => $message->recipient->id,
                            'recipient_first_name' => $message->recipient->first_name,
                            'recipient_last_name' => $message->recipient->last_name,
                            'recipient_email' => $message->recipient->email,

                            'listing_id' => $message->listing->id,
                            'listing_title' => $message->listing->title,
                            'listing_type_singular' => $type->name_singular,
                            'listing_type_plural' => $type->name_plural,

                            'message_id' => $message->id,
                            'message_title' => $message->title,
                            'message_description' => $message->description,

                            'link' => route('account/messages/' . $message->id),
                        ],
                        [$message->recipient->email => $message->recipient->getName()],
                        [config()->email->from_email => config()->email->from_name]
                    );
                }

                return redirect(adminRoute($type->slug . '-messages', session()->get('admin/' . $type->slug . '-messages')))
                    ->with('success', view('flash/success', ['message' => __('admin.messages.alert.create.success')]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/messages/create', [
                'type' => $type,
                'form' => $form,
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

        if (null === $message = \App\Models\Message::where('id', $params['id'])->where('type_id', $type->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.messages.title.update', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $form = form()
            ->add('active', 'toggle', ['label' => __('admin.messages.form.label.approved'), 'value' => (($type->approvable_messages == '1') ? null : '1')])
            ->add('sender_id', 'user', ['label' => __('admin.messages.form.label.from'), 'constraints' => 'required'])
            ->add('listing_id', 'listing', ['label' => __('admin.messages.form.label.listing'), 'constraints' => 'required|listing:' . $type->id, 'type' => $type->id])
            ->bindModel($message)
            ->add('submit', 'submit', ['label' => __('admin.messages.form.label.update')])
            ->setValues($message->data->pluck('value', 'field_name')->all())
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                if ($message->sender_id != $input->get('sender_id')) {
                    $message->replies()->where('user_id', $message->sender_id)->update(['user_id' => $input->get('sender_id')]);
                }
                
                if ($message->listing_id != $input->get('listing_id')) {
                    if (null !== $listing = \App\Models\Listing::find($input->get('listing_id'))) {
                        $message->replies()->where('user_id', $message->recipient_id)->update(['user_id' => $listing->get('user_id')]);
                    }
                }

                $message->sender_id = $input->get('sender_id');
                $message->listing_id = $input->get('listing_id');
                $message->recipient_id = $message->listing->user_id;

                if ($message->active != $input->active) {
                    if (null !== $input->active) {
                        $message->approve();
                    } else {
                        $message->disapprove();
                    }
                }

                $message->updated_datetime = date('Y-m-d H:i:s');
                $message->saveWithData($input);

                return redirect(adminRoute($type->slug . '-messages', session()->get('admin/' . $type->slug . '-messages')))
                    ->with('success', view('flash/success', ['message' => __('admin.messages.alert.update.success')]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/messages/update', [
                'type' => $type,
                'form' => $form,
                'message' => $message,
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

        if (null === $message = \App\Models\Message::where('id', $params['id'])->where('type_id', $type->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $message->delete();

        return redirect(adminRoute($type->slug . '-messages', session()->get('admin/' . $type->slug . '-messages')))
            ->with('success', view('flash/success', ['message' => __('admin.messages.alert.remove.success')]));
    }

    private function getTable($messages, $type)
    {
        return dataTable($messages)
            ->addColumns([
                'title' => [__('admin.messages.datatable.label.subject')],
                'from' => [__('admin.messages.datatable.label.from'), function ($message) {
                    return $message->sender->getName();
                }],
                'added_datetime' => [__('admin.messages.datatable.label.added_datetime'), function ($message) {
                    return locale()->formatDatetime($message->added_datetime, auth()->user()->timezone);
                }],
                'lastreply_datetime' => [__('admin.messages.datatable.label.last_reply'), function ($message) {
                    if (null !== $message->lastreply_datetime) {
                        return locale()->formatDatetimeDiff($message->lastreply_datetime);
                    }
                }],
                'active' => [__('admin.messages.datatable.label.approved'), function ($message) {
                    return view('misc/ajax-switch', [
                        'table' => 'messages',
                        'column' => 'active',
                        'id' => $message->id,
                        'value' => $message->active
                    ]);
                }],                        
            ])
            ->orderColumns([
                'added_datetime',
                'lastreply_datetime',
            ])
            ->addActions([
                'edit' => [__('admin.messages.datatable.action.edit'), function ($message) use ($type) {
                    return adminRoute($type->slug . '-messages/update/' . $message->id);
                }],
                'replies' => [
                    function ($message) {
                        return __('admin.messages.datatable.action.replies', ['count' => '<span class="badge badge-secondary">' . $message->replies->count() . '</span>']);
                    },
                    function ($message) use ($type) {
                        return adminRoute($type->slug . '-replies', ['message_id' => $message->id]);
                    },
                ],
                'delete' => [__('admin.messages.datatable.action.delete'), function ($message) use ($type) {
                    return adminRoute($type->slug . '-messages/delete/' . $message->id);
                }],
            ])
            ->addBulkActions([
                'approve' => __('admin.messages.datatable.bulkaction.approve'),
                'delete' => __('admin.messages.datatable.bulkaction.delete'),
            ]);
    }

}
