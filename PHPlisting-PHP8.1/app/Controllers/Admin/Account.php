<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Account
    extends \App\Src\Mvc\BaseController
{

    public function actionLogin($params)
    {
        layout()
            ->setHeader('admin/header')
            ->setFooter('admin/footer')
            ->setWrapper('admin/wrapper');

        if (false !== auth()->check('admin_login')) {
            return redirect(adminRoute(''));
        } else {
            layout()->setTitle(__('admin.index.title.login'));

            auth()->setProvider('native');

            $form = form()
                ->add('email', 'email', ['label' => __('account.form.label.email'), 'constraints' => 'required'])
                ->add('password', 'password', ['label' => __('account.form.label.password'), 'constraints' => 'required|password'])
                ->add('remember', 'toggle', ['label' => __('account.form.label.remember')])
                ->add('return', 'hidden', ['value' => session()->get('return')])
                ->add('token', 'token')
                ->add('submit', 'submit', ['label' => __('account.form.label.login')])
                ->add('reminder', 'button', ['label' => __('account.form.label.password_reminder'), 'attributes' => ['href' => route('account/password-reminder')]])
                ->handleRequest();

            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $input = $form->getValues();

                    if (auth()->attempt(['email' => $input->email, 'password' => $input->password], (null !== $input->get('remember') ? true : false))) {
                        if (null !== $input->get('return') && '' != $input->get('return')) {
                            return redirect($input->get('return'));
                        }

                        return redirect(adminRoute(''));
                    } else {
                        $alert = view('flash/error', ['message' => [__('account.alert.login.failure')]]);
                    }
                } else {
                    $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
                }
            }

            return response(layout()->content(
                view('admin/account/login', [
                    'form' => $form,
                    'alert' => $alert ?? null,
                ])
            ));
        }
    
    }

    public function actionLogout()
    {
        auth()->logout();

        return redirect(adminRoute('login'));
    }

}
