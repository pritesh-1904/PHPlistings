<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Languages
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.languages.title.index'));

        $languages = \App\Models\Language::search(null, [], 'admin/languages')
            ->orderBy('weight')
            ->paginate();

        $table = dataTable($languages)
            ->addColumns([
                'name' => [__('admin.languages.datatable.label.name')],
                'native' => [__('admin.languages.datatable.label.native')],
                'direction' => [__('admin.languages.datatable.label.direction'), function ($language) {
                        return __('admin.languages.form.label.direction.' . $language->direction);
                }],
                'active' => [__('admin.languages.datatable.label.published'), function ($language) {
                    if (null === $language->customizable) {
                        return null;
                    }

                    return view('misc/ajax-switch', [
                        'table' => 'languages',
                        'column' => 'active',
                        'id' => $language->id,
                        'value' => $language->active
                    ]);
                }],
            ])
            ->orderColumns([
                'name',
            ])
            ->addActions([
                'edit' => [__('admin.languages.datatable.action.edit'), function ($language) {
                    return adminRoute('languages/update/' . $language->id);
                }],
                'delete' => [__('admin.languages.datatable.action.delete'), function ($language) {
                    if (null === $language->customizable)  {
                        return null;
                    }

                    return adminRoute('languages/delete/' . $language->id);
                }],
            ])
            ->setSortable('languages');

        return response(layout()->content(
            view('admin/languages/index', [
                'languages' => $table,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (!auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.languages.title.create'));

        $language = new \App\Models\Language();

        $form = $this->getForm($language)
            ->add('submit', 'submit', ['label' => __('admin.languages.form.label.submit')])
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                $language->customizable = 1;
                $language->save();

                return redirect(adminRoute('languages', session()->get('admin/languages')))
                    ->with('success', view('flash/success', ['message' => __('admin.languages.alert.create.success', ['name' => $language->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/languages/create', [
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionUpdate($params)
    {
        if (!auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $language = \App\Models\Language::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.languages.title.update'));

        $form = $this->getForm($language);

        if (null === $language->customizable) {
            $form
                ->remove('active')
                ->remove('locale');
        }

        $form
            ->add('submit', 'submit', ['label' => __('admin.languages.form.label.update')])
            ->handleRequest();

        if ($form->isSubmitted()) {           
            $input = $form->getValues();

            if ($form->isValid()) {
                $language->save();

                return redirect(adminRoute('languages', session()->get('admin/languages')))
                    ->with('success', view('flash/success', ['message' => __('admin.languages.alert.update.success', ['name' => $language->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/languages/update', [
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionDelete($params)
    {
        if (!auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $language = \App\Models\Language::where('id', $params['id'])->whereNotNull('customizable')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $language->delete();

        return redirect(adminRoute('languages', session()->get('admin/languages')))
            ->with('success', view('flash/success', ['message' => __('admin.languages.alert.remove.success', ['name' => $language->name])]));
    }

    private function getForm($model)
    {
        return form($model)
            ->add('active', 'toggle', ['label' => __('admin.languages.form.label.published'), 'value' => 1])
            ->add('locale', 'text', ['label' => __('admin.languages.form.label.locale'), 'constraints' => 'required|alphanumericdash|maxlength:12|unique:languages,locale' . (null !== $model->get($model->getPrimaryKey()) ? ',' . $model->get($model->getPrimaryKey()) : '')])
            ->add('name', 'text', ['label' => __('admin.languages.form.label.name'), 'constraints' => 'required|alpha|maxlength:120'])
            ->add('native', 'text', ['label' => __('admin.languages.form.label.native'), 'constraints' => 'required|maxlength:120'])
            ->add('direction', 'select', ['label' => __('admin.languages.form.label.direction'), 'options' => ['ltr' => __('admin.languages.form.label.direction.ltr'), 'rtl' => __('admin.languages.form.label.direction.rtl')], 'constraints' => 'required'])
            ->add('thousands_separator', 'select', ['label' => __('admin.languages.form.label.thousands_separator'), 'options' => ['' => __('admin.languages.form.label.thousands_separator.none'), '.' => __('admin.languages.form.label.thousands_separator.dot'), ',' => __('admin.languages.form.label.thousands_separator.comma'), '\'' => __('admin.languages.form.label.thousands_separator.apostrophe')]])
            ->add('decimal_separator', 'select', ['label' => __('admin.languages.form.label.decimal_separator'), 'options' => ['.' => __('admin.languages.form.label.decimal_separator.dot'), ',' => __('admin.languages.form.label.decimal_separator.comma')], 'constraints' => 'required|notequalto:thousands_separator'])
            ->add('date_format', 'select', ['label' => __('admin.languages.form.label.date_format'), 'options' => ['m/d/Y' => 'MM/DD/YYYY', 'm-d-Y' => 'MM-DD-YYYY', 'd/m/Y' => 'DD/MM/YYYY', 'd-m-Y' => 'DD-MM-YYYY', 'Y/m/d' => 'YYYY/MM/DD', 'Y-m-d' => 'YYYY-MM-DD'], 'constraints' => 'required'])
            ->add('time_format', 'select', ['label' => __('admin.languages.form.label.time_format'), 'options' => ['h:i A' => __('admin.languages.form.label.time_format.12'), 'H:i' => __('admin.languages.form.label.time_format.24')], 'constraints' => 'required']);
    }

}
