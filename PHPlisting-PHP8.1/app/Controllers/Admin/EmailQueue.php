<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class EmailQueue
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check('admin_emails')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.emailqueue.title.index'));

        if (null !== request()->post->get('action') && null !== request()->post->get('id')) {
            switch (request()->post->action) {
                case 'approve':
                    db()->table('emails')
                        ->whereIn('id', (array) request()->post->id)
                        ->where('status', 'pending')
                        ->update(['status' => 'queued']);

                    $alert = view('flash/success', ['message' => __('admin.emailqueue.alert.approve.success')]);
                    break;
                case 'resend':
                    db()->table('emails')
                        ->whereIn('id', (array) request()->post->id)
                        ->update(['status' => 'queued', 'error' => null, 'failed' => null, 'processed_datetime' => null]);

                    $alert = view('flash/success', ['message' => __('admin.emailqueue.alert.resend.success')]);
                    break;
                case 'delete':
                    db()->table('emails')
                        ->whereIn('id', (array) request()->post->id)
                        ->delete();

                    $alert = view('flash/success', ['message' => __('admin.emailqueue.alert.remove.success')]);
                    break;
            }
        }

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'desc';
        }

        $emails = \App\Models\Email::search(null, [], 'admin/email-queue')
            ->with('template')
            ->paginate();

        $table = dataTable($emails)
            ->addColumns([
                'status' => [__('admin.emailqueue.datatable.label.status'), function ($email) {
                    return view('misc/status', [
                        'type' => 'email',
                        'status' => $email->status,
                    ]);
                }],
                'to' => [__('admin.emailqueue.datatable.label.to'), function ($email) {
                    return key($email->getTo());
                }],
                'template' => [__('admin.emailqueue.datatable.label.template'), function ($email) {
                    if (null !== $template = $email->getTemplate()) {
                        return $template->name;                       
                    }
                }],
                'added_datetime' => [__('admin.emailqueue.datatable.label.added_datetime'), function ($email) {
                    return locale()->formatDatetime($email->added_datetime, auth()->user()->timezone);
                }],
            ])
            ->addActions([
                'approve' => [__('admin.emailqueue.datatable.action.approve'), function ($email) {
                    if ('pending' == $email->status) {
                        return adminRoute('email-queue/approve/' . $email->id);
                    }

                    return null;
                }],
                'view' => [__('admin.emailqueue.datatable.action.view'), function ($email) {
                    return adminRoute('email-queue/view/' . $email->id);
                }],
                'delete' => [__('admin.emailqueue.datatable.action.delete'), function ($email) {
                    return adminRoute('email-queue/delete/' . $email->id);
                }],
            ])
            ->orderColumns([
                'id',
                'added_datetime',
            ])
            ->addBulkActions([
                'approve' => __('admin.emailqueue.datatable.bulkaction.approve'),
                'resend' => __('admin.emailqueue.datatable.bulkaction.resend'),
                'delete' => __('admin.emailqueue.datatable.bulkaction.delete'),
            ]);

        $form = form()
            ->setMethod('get')
            ->setTemplate('form/inline')
            ->add('recipient_id', 'user', [
                'placeholder' => __('admin.emailqueue.searchform.placeholder.recipient'),
                'weight' => 10
            ])
            ->add('submit', 'submit', [
                'label' => __('admin.emailqueue.searchform.label.submit')
            ])
            ->forceRequest();

        return response(layout()->content(
            view('admin/email-queue/index', [
                'alert' => $alert ?? null,
                'form' => $form,
                'emails' => $table,
            ])
        ));
    }

    public function actionView($params)
    {
        if (!auth()->check('admin_emails')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $email = \App\Models\Email::where('id', $params['id'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.emailqueue.title.view'));

        return response(layout()->content(
            view('admin/email-queue/view', [
                'email' => $email,
            ])
        ));
    }

    public function actionApprove($params)
    {
        if (!auth()->check('admin_emails')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $email = \App\Models\Email::where('id', $params['id'])->where('status', 'pending')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $email->status = 'queued';
        $email->save();

        return redirect(adminRoute('email-queue', session()->get('admin/email-queue')))
            ->with('success', view('flash/success', ['message' => __('admin.emailqueue.alert.approve.success')]));
    }

    public function actionDelete($params)
    {
        if (!auth()->check('admin_emails')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $email = \App\Models\Email::where('id', $params['id'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $email->delete();

        return redirect(adminRoute('email-queue', session()->get('admin/email-queue')))
            ->with('success', view('flash/success', ['message' => __('admin.emailqueue.alert.remove.success')]));
    }

}
