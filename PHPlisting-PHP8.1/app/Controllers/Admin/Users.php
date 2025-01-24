<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Users
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check('admin_users')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.users.title.index'));

        if (null !== request()->post->get('action') && null !== request()->post->get('id')) {
            switch (request()->post->action) {
                case 'approve':
                    $users = \App\Models\User::whereNull('active')->whereIn('id', (array) request()->post->get('id'))->get();

                    foreach ($users as $user) {
                        $user->approve()->save();
                    }

                    $alert = view('flash/success', ['message' => __('admin.users.alert.multiple_approve.success')]);
                    break;
                case 'verify':
                    db()->table('users')
                        ->whereIn('id', (array) request()->post->get('id'))
                        ->update(['email_verified' => 1]);

                    $alert = view('flash/success', ['message' => __('admin.users.alert.multiple_email_verify.success')]);
                    break;
                case 'delete':
                    $users = \App\Models\User::whereIn('id', (array) request()->post->get('id'))->get();

                    foreach ($users as $user) {
                        $user->delete();
                    }

                    $alert = view('flash/success', ['message' => __('admin.users.alert.multiple_remove.success')]);
                    break;
            }
        }

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'asc';
        }

        $users = \App\Models\User::search(
                (new \App\Models\User)
                    ->setSearchable('email', 'eq'),
                [], 'admin/users'
            )
            ->with('account.group')
            ->paginate();

        $form = form()
            ->setMethod('get')
            ->setTemplate('form/inline')
            ->add('keyword', 'text', [
                'placeholder' => __('admin.users.searchform.placeholder.name'),
                'weight' => 10
            ])
            ->add('email', 'text', [
                'placeholder' => __('admin.users.searchform.placeholder.email'),
                'weight' => 20
            ])
            ->add('submit', 'submit', [
                'label' => __('admin.users.searchform.label.submit')
            ])
            ->forceRequest();
            
        return response(layout()->content(
            view('admin/users/index', [
                'form' => $form,
                'users' => $this->getTable($users),
                'alert' => $alert ?? null,
            ])
        ));
    }

    public function actionSummary($params)
    {
        if (!auth()->check('admin_users')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $user = \App\Models\User::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.users.title.summary'));

        return response(layout()->content(
            view('admin/users/summary', [
                'user' => $user,
            ])
        ));
    }

    public function actionApprove($params)
    {
        if (!auth()->check('admin_users')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.users.title.approve'));

        if (null !== request()->post->get('action') && null !== request()->post->get('id')) {
            switch (request()->post->action) {
                case 'approve':
                    $users = \App\Models\User::whereNull('active')->whereIn('id', (array) request()->post->get('id'))->get();

                    foreach ($users as $user) {
                        $user->approve()->save();
                    }

                    $alert = view('flash/success', ['message' => __('admin.users.alert.multiple_approve.success')]);
                    break;
                case 'verify':
                    db()->table('users')
                        ->whereIn('id', (array) request()->post->get('id'))
                        ->update(['email_verified' => 1]);

                    $alert = view('flash/success', ['message' => __('admin.users.alert.multiple_email_verify.success')]);
                    break;
                case 'delete':
                    $users = \App\Models\User::whereIn('id', (array) request()->post->get('id'))->get();

                    foreach ($users as $user) {
                        $user->delete();
                    }

                    $alert = view('flash/success', ['message' => __('admin.users.alert.multiple_remove.success')]);
                    break;
            }
        }

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'desc';
        }

        $users = \App\Models\User::search(null, [], 'admin/users/approve')
            ->whereNull('active')
            ->with('account.group')
            ->paginate();

        $form = form()
            ->setMethod('get')
            ->setTemplate('form/inline')
            ->add('keyword', 'text', [
                'label' => __('admin.users.searchform.label.name'),
                'weight' => 10
            ])
            ->add('submit', 'submit', [
                'label' => __('admin.users.searchform.label.submit')
            ])
            ->forceRequest();
            
        return response(layout()->content(
            view('admin/users/approve', [
                'form' => $form,
                'users' => $this->getTable($users, true),
                'alert' => $alert ?? null,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (!auth()->check('admin_users')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.users.title.create'));

        $user = new \App\Models\User();

        $groups = \App\Models\UserGroup::all()
            ->pluck(function ($group) {
                return $group->name . ' (id: ' . $group->id . ')';
            }, 'id')
            ->all();

        $form = $this->getForm($user, $groups, 'submit')
            ->add('submit', 'submit', ['label' => __('admin.users.form.label.submit')])
            ->remove('captcha')
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                $user->active = $input->active;
                $user->email_verified = $input->email_verified;
                $user->taxable = $input->taxable;
                $user->banned = $input->banned;
                $user->added_datetime = date('Y-m-d H:i:s');

                unset($user->password);

                if (false !== $user->saveWithData($input)) {
                    $account = new \App\Models\Account();
                    $account->balance = $input->balance;
                    $account->setPassword($input->password);
                    $account->provider = 'native';
                    $account->usergroup_id = $input->usergroup_id;
                    $user->account()->save($account);
                }

                return redirect(adminRoute('users', session()->get('admin/users')))
                    ->with('success', view('flash/success', ['message' => __('admin.users.alert.create.success', ['id' => $user->id, 'name' => $user->getName()])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/users/create', [
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionUpdate($params)
    {
        if (!auth()->check('admin_users')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $user = \App\Models\User::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.users.title.update'));

        $groups = \App\Models\UserGroup::all()
            ->pluck(function ($group) {
                return $group->name . ' (id: ' . $group->id . ')';
            }, 'id')
            ->all();

        $form = $this->getForm($user, $groups, 'update')
            ->add('submit', 'submit', ['label' => __('admin.users.form.label.update')])
            ->remove('captcha');

        $form->get('password')->removeConstraint('required');

        if ($user->account->provider != 'native') {
            $form->remove('password');
        }

        $form
            ->setValues([
                'usergroup_id' => $user->account->usergroup_id,
                'balance' => $user->account->balance
            ])
            ->setValues($user->data->pluck('value', 'field_name')->all())
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                if ($user->active != $input->active) {
                    if (null !== $input->active) {
                        $user->approve();
                    } else {
                        $user->disapprove();
                    }
                }

                $user->email_verified = $input->email_verified;
                $user->taxable = $input->taxable;
                $user->banned = $input->banned;
                $user->token = $input->token;

                if (isset($user->password)) {
                    unset($user->password);
                }

                $user->saveWithData($input);

                $user->account->balance = $input->balance;
                $user->account->usergroup_id = $input->usergroup_id;

                if ($user->account->provider == 'native' && null !== $input->password && '' != $input->password) {
                    $user->account->setPassword($input->password);
                }

                $user->account->save();

                if (null !== request()->get->get('approval')) {
                    return redirect(adminRoute('users/approve', session()->get('admin/users/approve')))
                        ->with('success', view('flash/success', ['message' => __('admin.users.alert.update.success', ['id' => $user->id, 'name' => $user->getName()])]));
                } else {
                    return redirect(adminRoute('users', session()->get('admin/users')))
                        ->with('success', view('flash/success', ['message' => __('admin.users.alert.update.success', ['id' => $user->id, 'name' => $user->getName()])]));
                }
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/users/update', [
                'user' => $user,
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionDelete($params)
    {
        if (!auth()->check('admin_users')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $user = \App\Models\User::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ('1' != $user->id) {
            $user->delete();
        }

        return redirect(adminRoute('users', session()->get('admin/users')))
            ->with('success', view('flash/success', ['message' => __('admin.users.alert.remove.success', ['id' => $user->id, 'name' => $user->getName()])]));
    }

    private function getTable($users, $approval = false)
    {
        return dataTable($users)
            ->addColumns([
                'id' => [__('admin.users.datatable.label.id')],
                'first_name' => [__('admin.users.datatable.label.first_name')],
                'last_name' => [__('admin.users.datatable.label.last_name')],
                'group' => [__('admin.users.datatable.label.group'), function ($user) {
                    return $user->account->group->name;
                }],
                'active' => [__('admin.users.datatable.label.approved'), function ($user) {
                    return view('misc/ajax-switch', [
                        'table' => 'users',
                        'column' => 'active',
                        'id' => $user->id,
                        'value' => $user->active
                    ]);
                }],
                'email_verified' => [__('admin.users.datatable.label.email_verified'), function ($user) {
                    return view('misc/ajax-switch', [
                        'table' => 'users',
                        'column' => 'email_verified',
                        'id' => $user->id,
                        'value' => $user->email_verified
                    ]);
                }],
                'banned' => [__('admin.users.datatable.label.banned'), function ($user) {
                    return view('misc/ajax-switch', [
                        'table' => 'users',
                        'column' => 'banned',
                        'id' => $user->id,
                        'value' => $user->banned
                    ]);
                }],
            ])
            ->orderColumns([
                'id',
                'last_name',
                'active',
                'email_verified',
            ])
            ->addActions([
                'summary' => [__('admin.users.datatable.action.summary'), function ($user) {
                    return adminRoute('users/summary/' . $user->id);
                }],
                'edit' => [__('admin.users.datatable.action.edit'), function ($user) use ($approval) {
                    return adminRoute('users/update/' . $user->id, ['approval' => (false !== $approval ? 'true' : null)]);
                }],
                'delete' => [__('admin.users.datatable.action.delete'), function ($user) {
                    return adminRoute('users/delete/' . $user->id);
                }],
            ])
            ->addBulkActions([
                'approve' => __('admin.users.datatable.bulkaction.approve'),
                'verify' => __('admin.users.datatable.bulkaction.verify'),
                'delete' => __('admin.users.datatable.bulkaction.delete'),
            ]);
    }

    private function getForm($model, $groups, $type)
    {
        return form()
            ->add('active', 'toggle', ['label' => __('admin.users.form.label.approved'), 'value' => ((config()->account->approval == '1') ? null : '1')])
            ->add('email_verified', 'toggle', ['label' => __('admin.users.form.label.email_verified'), 'value' => ((config()->account->verification == '1') ? null : '1')])
            ->add('taxable', 'toggle', ['label' => __('admin.users.form.label.taxable'), 'value' => 1])
            ->add('banned', 'toggle', ['label' => __('admin.users.form.label.banned')])
            ->add('usergroup_id', 'select', ['label' => __('admin.users.form.label.group'), 'options' => $groups, 'value' => config()->account->default_group])
            ->add('balance', 'price', ['label' => __('admin.users.form.label.balance'), 'value' => '0.00', 'constraints' => 'required|max:9999999999999.99'])
            ->add('token', 'hash', ['label' => __('admin.users.form.label.token'), 'value' => bin2hex(random_bytes(16)), 'constraints' => 'required'])
            ->bindModel($model, $type)
            ->setValue('timezone', config()->general->timezone);
    }

}
