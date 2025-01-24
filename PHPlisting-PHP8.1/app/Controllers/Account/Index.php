<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Account;

class Index
    extends \App\Controllers\Account\BaseController
{

    public function actionIndex($params)
    {
        if (null === $page = \App\Models\Page::where('slug', 'account')->first()) {
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

        $data = collect([
            'page' => $page,
            'html' => view('account/index', []),
        ]);

        $response = $page->render($data);

        if ($response instanceof \App\Src\Http\RedirectResponse) {
            return $response;
        }

        return response(
            layout()->content($response)
        );
    }

    public function actionProfile($params)
    {
        if (null === $page = \App\Models\Page::where('slug', 'account/profile')->first()) {
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

        $user = auth()->user();

        $form = form($user, 'update')
            ->add('submit', 'submit', ['label' => __('account.form.label.update')]);

        $form->get('password')->removeConstraint('required');

        if ($user->account->provider != 'native') {
            $form->remove('password');
        }

        $form
            ->setValues($user->data->pluck('value', 'field_name')->all())        
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                $user->updated_datetime = date('Y-m-d H:i:s');

                if (isset($user->password)) {
                    unset($user->password);
                }
                
                $user->saveWithData($input);

                if ($user->account->provider == 'native' && '' != $input->get('password', '')) {
                    $user->account->setPassword($input->get('password'));
                    $user->account->save();
                }

                (new \App\Repositories\EmailQueue())->push(
                    'admin_account_updated',
                    null,
                    [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'link' => adminRoute('users/summary/' . $user->id),
                    ],
                    [config()->email->from_email => config()->email->from_name],
                    [config()->email->from_email => config()->email->from_name]
                );

                return redirect(route('account'))
                    ->with('success', view('flash/success', ['message' => __('account.form.alert.update.success')]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        $data = collect([
            'page' => $page,
            'html' => view('account/profile', [
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

}
