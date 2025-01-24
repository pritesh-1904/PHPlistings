<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Settings
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (!isset($params['group'])) {
            return redirect(adminRoute('settings/general'));
        }

        layout()->setTitle(__('admin.settings.title.index'));

        if (null === $group = \App\Models\SettingGroup::where('slug', $params['group'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $form = form($group)
            ->add('submit', 'submit', ['label' => __('admin.settings.form.label.submit')])
            ->setValues($group->settings()->get()->pluck('value', 'name')->all())
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                foreach ($group->settings()->get() as $setting) {
                    $setting->value = $input->get($setting->name);
                    $setting->save();
                }

                $alert = view('flash/success', ['message' => __('admin.settings.alert.update.success')]);
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/settings/index', [
                'currentGroup' => $group,
                'groups' => \App\Models\SettingGroup::orderBy('id')->get(),
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

}
