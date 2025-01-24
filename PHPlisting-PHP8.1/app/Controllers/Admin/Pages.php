<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Pages
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check('admin_appearance')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.pages.title.index'));

        if (null === request()->get->get('sort')) {
            request()->get->put('sort', 'id');
            request()->get->put('sort_direction', 'asc');
        }

        $pages = \App\Models\Page::search(null, [], 'admin/pages')
            ->where(function ($query) {
                $query
                    ->whereHasNot('type')
                    ->orWhereHas('type', function ($query) {
                        $query->whereNull('deleted');
                    });
            })
            ->paginate();

        $form = form()
            ->setMethod('get')
            ->setTemplate('form/inline')
            ->add('slug', 'text', [
                'placeholder' => __('admin.pages.searchform.placeholder.slug'),
                'weight' => 10
            ])
            ->add('type_id', 'select', [
                'options' => ['' => __('admin.pages.searchform.option.type_id')] + (new \App\Models\Type())->whereNull('deleted')->orderBy('weight')->get()->pluck('name_plural', 'id')->all(),
                'weight' => 20
            ])
            ->add('submit', 'submit', [
                'label' => __('admin.pages.searchform.label.submit'),
            ])
            ->handleRequest();

        $table = dataTable($pages)
            ->addColumns([
                'id' => [__('admin.pages.datatable.label.id')],
                'title' => [__('admin.pages.datatable.label.title')],
                'slug' => [__('admin.pages.datatable.label.slug')],
                'type' => [__('admin.pages.datatable.label.type'), function ($page) {
                    if (null !== $page->type_id && null !== $page->type) {
                        return $page->type->name_plural . ' (id:' . $page->type->id . ')';
                    }
                }],
                'active' => [__('admin.pages.datatable.label.active'), function ($page) {
                    return view('misc/ajax-switch', [
                        'table' => 'pages',
                        'column' => 'active',
                        'id' => $page->id,
                        'value' => $page->active,
                    ]);
                }],
            ])
            ->orderColumns([
                'id',
                'title',
                'slug',
            ])
            ->addActions([
                'edit' => [__('admin.pages.datatable.action.edit'), function ($page) {
                    return adminRoute('widgets/' . $page->id);
                }],
                'delete' => [__('admin.pages.datatable.action.delete'), function ($page) {
                    if (null !== $page->customizable) {
                        return adminRoute('pages/delete/' . $page->id);
                    }
                }],
            ]);

        return response(layout()->content(
            view('admin/pages/index', [
                'pages' => $table,
                'form' => $form,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (!auth()->check('admin_appearance')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.pages.title.create'));

        $page = new \App\Models\Page();

        $form = $this->getForm($page)
            ->add('submit', 'submit', ['label' => __('admin.pages.form.label.submit')])
            ->handleRequest();
        
        if ($form->isSubmitted()) {           
            if ($form->isValid()) {
                $page->customizable = 1;

                if (false !== $page->save()) {
                    foreach ($page->getCustomPageDefaultWidgets() as $widget) {
                        $page->addWidget($widget);
                    }
                }

                return redirect(adminRoute('pages', session()->get('admin/pages')))
                    ->with('success', view('flash/success', ['message' => __('admin.pages.alert.create.success', ['id' => $page->id, 'slug' => $page->slug])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/pages/create', [
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

        if (null === $page = \App\Models\Page::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.pages.title.update'));

        $form = $this->getForm($page);

        if (null === $page->customizable) {
            $form->remove('slug');
        }

        $form
            ->add('submit', 'submit', ['label' => __('admin.pages.form.label.update')])
            ->handleRequest();

        if ($form->isSubmitted()) {           
            $input = $form->getValues();

            if ($form->isValid()) {
                $page->save();

                return redirect(adminRoute('widgets/' . $page->id, session()->get('admin/widgets/' . $page->id)))
                    ->with('success', view('flash/success', ['message' => __('admin.pages.alert.update.success', ['id' => $page->id, 'slug' => $page->slug])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/pages/update', [
                'form' => $form,
                'page' => $page,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionDelete($params)
    {
        if (!auth()->check('admin_appearance')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $page = \App\Models\Page::where('id', $params['id'])->whereNotNull('customizable')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $page->delete();

        return redirect(adminRoute('pages', session()->get('admin/pages')))
            ->with('success', view('flash/success', ['message' => __('admin.pages.alert.remove.success', ['id' => $page->id, 'slug' => $page->slug])]));
    }

    private function getForm($model)
    {
        return form($model)
            ->add('active', 'toggle', ['label' => __('admin.pages.form.label.active'), 'value' => 1])
            ->add('title', 'translatable', ['label' => __('admin.pages.form.label.title'), 'constraints' => 'transrequired'])
            ->add('slug', 'text', ['label' => __('admin.pages.form.label.slug'), 'sluggable' => 'title', 'constraints' => 'required|alphanumericdash|maxlength:120|unique:pages,slug' . (null !== $model->get($model->getPrimaryKey()) ? ',' . $model->get($model->getPrimaryKey()) : '')])
            ->add('meta_title', 'translatable', ['label' => __('admin.pages.form.label.meta_title')])
            ->add('meta_keywords', 'translatable', ['label' => __('admin.pages.form.label.meta_keywords')])
            ->add('meta_description', 'translatable', ['label' => __('admin.pages.form.label.meta_description')]);
    }

}
