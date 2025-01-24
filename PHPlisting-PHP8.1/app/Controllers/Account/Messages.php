<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Account;

class Messages
    extends \App\Controllers\Account\BaseController
{

    public function actionIndex($params)
    {
        if (null === $page = \App\Models\Page::where('slug', 'account/messages')->first()) {
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

        $messages = \App\Models\Message::search()
            ->whereHas('type', function ($query) {
                $query->whereNull('deleted');

                if (false === auth()->check('admin_login')) {
                    $query->whereNotNull('active');
                }
            })
            ->where(function ($query) {
                return $query
                    ->where(function ($query) {
                        $query
                            ->whereNotNull('active')
                            ->where('recipient_id', auth()->user()->id);
                    })
                    ->orWhere('sender_id', auth()->user()->id);
            })
            ->with([
                'recipient',
                'sender',
            ])
            ->with('replies', function ($query) {
                return $query
                    ->whereNotNull('active')
                    ->where('user_id', '!=', auth()->user()->id)
                    ->whereNull('read_datetime');
            })
            ->paginate();

        $table = dataTable($messages)
            ->addColumns([
                'title' => [__('message.datatable.label.subject'), function ($message) {
                    if (($message->recipient->id == auth()->user()->id && null === $message->read_datetime) || $message->replies->count() > 0) {
                        return '<a href="' . route('account/messages/' . $message->id) . '"><strong>' . $message->title . '</strong></a>';
                    } else {
                        return '<a href="' . route('account/messages/' . $message->id) . '">' . $message->title . '</a>';
                    }
                }],
                'from' => [__('message.datatable.label.from'), function ($message) {
                    if ($message->sender->id == auth()->user()->id) {
                        return __('message.datatable.label.me');
                    }

                    return $message->sender->getName();
                }],
                'to' => [__('message.datatable.label.to'), function ($message) {
                    if ($message->recipient->id == auth()->user()->id) {
                        return __('message.datatable.label.me');
                    }
                    
                    return $message->recipient->getName();
                }],
                'lastreply_datetime' => [__('message.datatable.label.last_reply'), function ($message) {
                    if (null !== $message->lastreply_datetime) {
                        return '<small>' . locale()->formatDatetimeDiff($message->lastreply_datetime) . '</small>';
                    }

                    return;
                }],
            ])
            ->orderColumns([
                'title',
                'lastreply_datetime',
            ]);

        $data = collect([
            'page' => $page,
            'html' => view('account/messages/index', [
                'messages' => $table,
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
        if (null === $page = \App\Models\Page::where('slug', 'account/messages/view')->first()) {
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

        if (null === $message = \App\Models\Message::where('id', $params['id'])
            ->whereHas('type', function ($query) {
                $query->whereNull('deleted');

                if (false === auth()->check('admin_login')) {
                    $query->whereNotNull('active');
                }
            })
            ->where(function ($query) {
                return $query
                    ->where(function ($query) {
                        $query
                            ->whereNotNull('active')
                            ->where('recipient_id', auth()->user()->id);
                    })
                    ->orWhere('sender_id', auth()->user()->id);
            })
            ->first())
        {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $message->read_datetime && $message->recipient->id == auth()->user()->id) {
            $message->read_datetime = date('Y-m-d H:i:s');
            $message->save();
        }

        $replies = $message->replies()
            ->where(function ($query) {
                $query
                    ->whereNotNull('active')
                    ->orWhere('user_id', auth()->user()->id);
            })
            ->orderBy('id', 'desc')
            ->with('user')
            ->paginate();

        foreach ($replies as $reply) {
            if (null === $reply->read_datetime && $reply->user_id != auth()->user()->id) {
                $reply->read_datetime = date('Y-m-d H:i:s');
                $reply->save();
            }
        }

        $reply = new \App\Models\Reply();

        $form = form($reply)
            ->add('description', 'textarea', ['label' => __('message.reply.form.label.message'), 'constraints' => 'required|maxlength:1000'])
            ->add('submit', 'submit', ['label' => __('message.reply.form.label.submit')])
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                if (null === $message->type->approvable_replies) {
                    $reply->active = 1;
                }
                
                $reply->type_id = $message->type_id;
                $reply->user_id = auth()->user()->id;
                $reply->added_datetime = date('Y-m-d H:i:s');

                $message->replies()->save($reply);

                $message->lastreply_datetime = date('Y-m-d H:i:s');
                $message->save();

                $recipient = ($message->sender->id == auth()->user()->id) ? $message->recipient : $message->sender;

                $emailData =                     [
                    'sender_id' => auth()->user()->id,
                    'sender_first_name' => auth()->user()->first_name,
                    'sender_last_name' => auth()->user()->last_name,
                    'sender_email' => auth()->user()->email,

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

                    'link' => adminRoute($message->type->slug . '-replies/approve'),
                ];

                (new \App\Repositories\EmailQueue())->push(
                    (null !== $reply->get('active') ? 'admin_reply_created' : 'admin_reply_created_approve'),
                    null,
                    $emailData,
                    [config()->email->from_email => config()->email->from_name],
                    [config()->email->from_email => config()->email->from_name]
                );

                if (null !== $reply->get('active')) {
                    $emailData['link'] = route('account/messages/' . $message->id);
                    
                    (new \App\Repositories\EmailQueue())->push(
                        'user_reply_created',
                        $recipient->id,
                        $emailData,
                        [$recipient->email => $recipient->getName()],
                        [config()->email->from_email => config()->email->from_name]
                    );
                }

                return redirect(route('account/messages/' . $message->id))
                    ->with('error', view('flash/success', ['message' => [
                        __(($reply->active == 1 ? 'message.reply.alert.create.success' : 'message.reply.alert.create_with_moderation.success'))
                    ]]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        $data = collect([
            'page' => $page,
            'html' => view('account/messages/view', [
                'form' => $form,
                'alert' => $alert ?? null,
                'message' => $message,
                'replies' => $replies,
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
