<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Account;

class Account
    extends \App\Src\Mvc\BaseController
{

    public function __construct()
    {
        layout()->setWrapper('account/wrapper');
    }

    public function actionLogin($params)
    {
        if (false !== auth()->check('user_login')) {
            return redirect(route('account'));
        }

        if (null === $page = \App\Models\Page::where('slug', 'account/login')->first()) {
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

        if (isset($params['provider'])) {
            auth()->setProvider($params['provider']);

            if (null !== auth()->getLoginUrl()) {
                return redirect(auth()->getLoginUrl());
            }
        }

        auth()->setProvider(config()->account->default_provider);

        $form = form()
            ->add('email', 'email', ['label' => __('account.form.label.email'), 'constraints' => 'required'])
            ->add('password', 'password', ['label' => __('account.form.label.password'), 'constraints' => 'required|password'])
            ->add('remember', 'toggle', ['label' => __('account.form.label.remember')])
            ->add('return', 'hidden', ['value' => session()->get('return')])
            ->add('captcha', 'captcha')
            ->add('token', 'token')
            ->add('submit', 'submit', ['label' => __('account.form.label.login')])
            ->add('reminder', 'button', ['label' => __('account.form.label.password_reminder'), 'attributes' => ['href' => route('account/password-reminder')]])
            ->handleRequest();

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $input = $form->getValues();

                if (false !== auth()->attempt(['email' => $input->get('email'), 'password' => $input->get('password')], (null !== $input->get('remember') ? true : false))) {
                    if (null !== $input->get('return') && '' != $input->get('return')) {

//                        session()->forget('return');
                        
                        return redirect(d($input->get('return')));
                    }

                    return redirect(route('account'));
                } else {
                    $alert = view('flash/error', ['message' => [__('account.alert.login.failure')]]);
                }
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        $data = collect([
            'page' => $page,
            'html' => view('account/login', [
                'form' => $form,
                'alert' => $alert ?? null,
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

    public function actionLogout()
    {
        auth()->logout();

        return redirect(route('account/login'));
    }

    public function actionCreate($params)
    {
        if (false !== auth()->check('user_login')) {
            return redirect(route('account'));
        }
        
        if (null === $page = \App\Models\Page::where('slug', 'account/create')->first()) {
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

        $user = new \App\Models\User;

        $form = form($user, 'submit')
            ->add('token', 'token')
            ->add('submit', 'submit', ['label' => __('account.form.label.submit')])
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            $user->taxable = 1;
            $user->added_datetime = date('Y-m-d H:i:s');

            if ($form->isValid()) {
                if (null === config()->account->approval) {
                    $user->active = 1;
                }

                if (null === config()->account->verification) {
                    $user->email_verified = 1;
                }

                if (null === $input->get('timezone')) {
                    $user->timezone = config()->general->timezone;
                }

                unset($user->password);
                
                if (false !== $user->saveWithData($input)) {
                    $account = new \App\Models\Account();
                    $account->setPassword($input->get('password'));
                    $account->provider = 'native';
                    $account->usergroup_id = config()->account->default_group;
                    $user->account()->save($account);
                }

                (new \App\Repositories\EmailQueue())->push(
                    (null !== config()->account->verification ? 'user_account_created_verify' : 'user_account_created'),
                    $user->id,
                    [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'link' => route('account/verification/' . $user->verification_code),
                        'code' => $user->verification_code,
                    ],
                    [$user->email => $user->getName()],
                    [config()->email->from_email => config()->email->from_name]
                );

                (new \App\Repositories\EmailQueue())->push(
                    (null !== config()->account->approval ? 'admin_account_created_approve' : 'admin_account_created'),
                    null,
                    [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'link' => adminRoute('users/approve'),
                    ],
                    [config()->email->from_email => config()->email->from_name],
                    [config()->email->from_email => config()->email->from_name]
                );

                if (null === config()->account->verification && null === config()->account->approval) {
                    session()->flash('success', view('flash/success', ['message' => __('account.form.alert.create.success')]));
                } else if (null !== config()->account->verification && null === config()->account->approval) {
                    session()->flash('success', view('flash/success', ['message' => __('account.form.alert.create_with_verification.success')]));
                } else if (null === config()->account->verification && null !== config()->account->approval) {
                    session()->flash('success', view('flash/success', ['message' => __('account.form.alert.create_with_moderation.success')]));
                } else {
                    session()->flash('success', view('flash/success', ['message' => __('account.form.alert.create_with_moderation_and_verification.success')]));
                }

                return redirect(route('account/login'));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        $data = collect([
            'page' => $page,
            'html' => view('account/create', [
                'form' => $form,
                'alert' => $alert ?? null,
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

    public function actionCallback($params)
    {
        if (isset($params['provider'])) {
            auth()->setProvider($params['provider']);

            if (false !== auth()->attempt()) {
                return redirect(route('account'));
            }
        }

        return redirect(route('account/login'))
            ->with('error', view('flash/error', ['message' => [__('account.alert.provider.failure')]]));
    }

    public function actionVerification($params)
    {
        if (false !== auth()->check('user_login')) {
            return redirect(route('account'));
        }

        if (null === $page = \App\Models\Page::where('slug', 'account/verification')->first()) {
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

        $form = form()
            ->add('verification', 'text', ['label' => __('account.form.label.verification_code'), 'value' => $params['code'] ?? null, 'constraints' => 'required'])
            ->add('captcha', 'captcha')
            ->add('token', 'token')
            ->add('submit', 'submit', ['label' => __('account.form.label.verify')])
            ->handleRequest();

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $input = $form->getValues();

                $user = \App\Models\User::where('verification_code', $input->verification)->first();

                if (null !== $user && null === $user->email_verified) {
                    $user->email_verified = 1;
                    $user->save();

                    return redirect(route('account/login'))
                        ->with('success', view('flash/success', ['message' => __('account.form.alert.verification.success')]));
                } else {
                    $alert = view('flash/error', ['message' => [__('account.form.alert.verification.failure')]]);
                }
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        $data = collect([
            'page' => $page,
            'html' => view('account/verification', [
                'form' => $form,
                'alert' => $alert ?? null,
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

    public function actionPasswordReminder($params)
    {
        if (false !== auth()->check('user_login')) {
            return redirect(route('account'));
        }

        if (null === $page = \App\Models\Page::where('slug', 'account/password-reminder')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()
            ->setTitle($page->title)
            ->setMeta('title', $page->meta_title)
            ->setMeta('keywords', $page->meta_keywords)
            ->setMeta('description', $page->meta_description);

        $form = form()
            ->add('email', 'email', ['label' => __('account.form.label.email'), 'constraints' => 'required'])
            ->add('captcha', 'captcha')
            ->add('token', 'token')
            ->add('submit', 'submit', ['label' => __('account.form.label.password_reset_request')])
            ->handleRequest();

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $input = $form->getValues();

                $user = \App\Models\User::where('email', $input->email)
                    ->whereHas('account', function ($query) {
                        $query->where('provider', 'native');
                    })
                    ->first();

                if (null !== $user) {
                    $reminder = new \App\Models\Reminder();
                    $reminder->user_id = $user->id;
                    $reminder->added_datetime = date("Y-m-d H:i:s");
                    $reminder->save();

                    (new \App\Repositories\EmailQueue())->push(
                        'user_password_reset',
                        $user->id,
                        [
                            'id' => $user->id,
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                            'email' => $user->email,
                            'link' => route('account/password-reset/' . $reminder->verification_code),
                            'code' => $reminder->verification_code,
                        ],
                        [$user->email => $user->getName()],
                        [config()->email->from_email => config()->email->from_name]
                    );
                }

                return redirect(route('account/login'))
                    ->with('success', view('flash/success', ['message' => __('account.form.alert.password_reset_request.success')]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        $data = collect([
            'page' => $page,
            'html' => view('account/password-reminder', [
                'form' => $form,
                'alert' => $alert ?? null,
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

    public function actionPasswordReset($params)
    {
        if (false !== auth()->check('user_login')) {
            return redirect(route('account'));
        }

        if (!isset($params['code'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $page = \App\Models\Page::where('slug', 'account/password-reset')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()
            ->setTitle($page->title)
            ->setMeta('title', $page->meta_title)
            ->setMeta('keywords', $page->meta_keywords)
            ->setMeta('description', $page->meta_description);
        
        if (null !== $reminder = \App\Models\Reminder::where('verification_code', $params['code'])->first()) {
            $form = form()
                ->add('password', 'password', ['label' => __('account.form.label.new_password'), 'constraints' => 'required'])
                ->add('captcha', 'captcha')
                ->add('token', 'token')
                ->add('submit', 'submit', ['label' => __('account.form.label.update')])
                ->handleRequest();

            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $input = $form->getValues();

                    $reminder->user->account->setPassword($input->password);
                    $reminder->user->account->save();

                    $reminder->delete();

                    (new \App\Repositories\EmailQueue())->push(
                        'user_password_reset_notification',
                        $reminder->user->id,
                        [
                            'id' => $reminder->user->id,
                            'first_name' => $reminder->user->first_name,
                            'last_name' => $reminder->user->last_name,
                            'email' => $reminder->user->email,
                            'password' => $input->password,
                        ],
                        [$reminder->user->email => $reminder->user->getName()],
                        [config()->email->from_email => config()->email->from_name]
                    );

                    return redirect(route('account/login'))
                        ->with('success', view('flash/success', ['message' => __('account.form.alert.password_reset.success')]));
                } else {
                    $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
                }
            }
        } else {
            $alert = view('flash/error', ['message' => __('account.form.alert.password_reset.failure')]);
        }

        $data = collect([
            'page' => $page,
            'html' => view('account/password-reset', [
                'form' => $form ?? null,
                'alert' => $alert ?? null,
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
