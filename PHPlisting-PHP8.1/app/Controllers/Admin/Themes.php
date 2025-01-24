<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Themes
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check('admin_appearance')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.themes.title.index'));

        $themes = \App\Models\Theme::search(null, [], 'admin/themes')
            ->orderBy('weight')
            ->paginate();

        $table = dataTable($themes)
            ->addColumns([
                'name' => [__('admin.themes.datatable.label.name')],
                'slug' => [__('admin.themes.datatable.label.slug')],
            ])
            ->addActions([
                'edit' => [__('admin.themes.datatable.action.edit'), function ($theme) {
                    return adminRoute('themes/update/' . $theme->slug);
                }],
                'delete' => [__('admin.themes.datatable.action.delete'), function ($theme) {
                    if (null !== $theme->customizable)  {
                        return adminRoute('themes/delete/' . $theme->slug);
                    }
                }],
            ])
            ->setSortable('themes');

        return response(layout()->content(
            view('admin/themes/index', [
                'themes' => $table,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (!auth()->check('admin_appearance')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.themes.title.create'));

        $theme = new \App\Models\Theme();

        $form = $this->getForm($theme)
            ->add('submit', 'submit', ['label' => __('admin.themes.form.label.submit')])
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                $theme->customizable = 1;
                $theme->version = 1;
                $theme->settings = '[]';
                $theme->save();

                return redirect(adminRoute('themes', session()->get('admin/themes')))
                    ->with('success', view('flash/success', ['message' => __('admin.themes.alert.create.success', ['name' => $theme->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/themes/create', [
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionUpdate($params)
    {
        if (!auth()->check('admin_appearance')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $theme = \App\Models\Theme::where('slug', $params['slug'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.themes.title.update'));

        $form = $this->getForm($theme)
            ->add('separator', 'separator');

        $theme
            ->getThemeSettingsObject()
            ->getConfigurationForm($form)
            ->setValues($theme->getThemeSettingsObject()->getSettings());

        if (null === $theme->get('customizable')) {
            $form->remove('slug');
        }

        $form
            ->add('submit', 'submit', ['label' => __('admin.themes.form.label.update', ['name' => $theme->name])])
            ->handleRequest();

        if ($form->isSubmitted()) {           
            $input = $form->getValues();

            if ($form->isValid()) {                
                $settings = [];

                foreach ($theme->getThemeSettingsObject()->getConfigurationForm(form())->getFields() as $field) {
                    $settings[$field->name] = $input->get($field->name);
                }

                $theme->setSettings($settings);
                $theme->version = $theme->version + 1;
                $theme->save();

                return redirect(adminRoute('themes', session()->get('admin/themes')))
                    ->with('success', view('flash/success', ['message' => __('admin.themes.alert.update.success', ['name' => $theme->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/themes/update', [
                'form' => $form,
                'alert' => $alert ?? null,
                'theme' => $theme,
            ])
        ));
    }

    public function actionDelete($params)
    {
        if (!auth()->check('admin_appearance')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (false === isset($params['slug']) || null === $theme = \App\Models\Theme::whereNotNull('customizable')->where('slug', $params['slug'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $theme->delete();
            
        return redirect(adminRoute('themes', session()->get('admin/themes')))
            ->with('success', view('flash/success', ['message' => __('admin.themes.alert.remove.success', ['name' => $theme->name])]));
    }

    private function getForm($model)
    {
        $form = form($model)->add('name', 'text', ['label' => __('admin.themes.form.label.name'), 'constraints' => 'required']);

        $form->add('slug', 'text', ['label' => __('admin.themes.form.label.slug'), 'constraints' => 'required|alpha|maxlength:100|unique:themes,slug' . (null !== $model->get($model->getPrimaryKey()) ? ',' . $model->get($model->getPrimaryKey()) : '')]);

        return $form;
    }

}
