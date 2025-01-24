<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Types
    extends \App\Controllers\Admin\BaseController
{

    public $types;

    public function __construct()
    {
        parent::__construct();

        $this->types = [
            'LocalBusiness' => __('admin.types.type.LocalBusiness'),
            'Product' => __('admin.types.type.Product'),
            'Event' => __('admin.types.type.Event'),
            'Offer' => __('admin.types.type.Offer'),
            'JobPosting' => __('admin.types.type.JobPosting'),
            'BlogPosting' => __('admin.types.type.BlogPosting'),
            'Place' => __('admin.types.type.Place'),
        ];
    }

    public function actionIndex($params)
    {
        if (!auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.types.title.index'));

        $types = \App\Models\Type::search(null, [], 'admin/types')
            ->whereNull('deleted')
            ->orderBy('weight')
            ->paginate();

        $table = dataTable($types)
            ->addColumns([
                'id' => [__('admin.types.datatable.label.id')],
                'name_plural' => [__('admin.types.datatable.label.name')],
                'slug' => [__('admin.types.datatable.label.slug')],
                'active' => [__('admin.types.datatable.label.active'), function ($type) {
                    return view('misc/ajax-switch', [
                        'table' => 'types',
                        'column' => 'active',
                        'id' => $type->id,
                        'value' => $type->active
                    ]);
                }],                        
            ])
            ->addActions([
                'edit' => [__('admin.types.datatable.action.edit'), function ($type) {
                    return adminRoute('types/update/' . $type->id);
                }],
                'delete' => [__('admin.types.datatable.action.delete'), function ($type) {
                    return adminRoute('types/delete/' . $type->id);
                }],
            ])
            ->setSortable('types');

        return response(layout()->content(
            view('admin/types/index', [
                'types' => $table,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (!auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.types.title.create'));

        $type = new \App\Models\Type();
        
        $form = $this->getForm($type)
            ->add('submit', 'submit', ['label' => __('admin.types.form.label.submit')])
            ->handleRequest();
        
        if ($form->isSubmitted()) {           
            $input = $form->getValues();

            if ($form->isValid()) {
                if (\App\Models\Type::where('slug', $input->get('slug'))->whereNull('deleted')->count() > 0) {
                    $form->setValidationError('slug', __('form.validation.unique'));
                }
            }

            if ($form->isValid()) {
                $type->save();

                $type->parents()->sync($input->get('parents'));
                $type->ratings()->sync($input->get('ratings'));

                return redirect(adminRoute('types', session()->get('admin/types')))
                    ->with('success', view('flash/success', ['message' => __('admin.types.alert.create.success', ['name_singular' => $type->name_singular, 'name_plural' => $type->name_plural])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/types/create', [
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

        if (null === $type = \App\Models\Type::whereNull('deleted')->where('id', $params['id'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.types.title.update'));

        $form = $this->getForm($type)
            ->remove([
                'localizable',
                'type',
            ])
            ->add('submit', 'submit', ['label' => __('admin.types.form.label.update')])
            ->setValues([
                'parents' => $type->parents()->get()->pluck('id')->all(),
                'ratings' => $type->ratings()->get()->pluck('id')->all(),
            ])
            ->handleRequest();

        if ($form->isSubmitted()) {           
            $input = $form->getValues();

            if ($form->isValid()) {
                if (\App\Models\Type::where('id', '!=', $type->id)->where('slug', $input->get('slug'))->whereNull('deleted')->count() > 0) {
                    $form->setValidationError('slug', __('form.validation.unique'));
                }
            }

            if ($form->isValid()) {
                $type->save();

                $type->parents()->sync($input->get('parents'));
                $type->ratings()->sync($input->get('ratings'));

                return redirect(adminRoute('types', session()->get('admin/types')))
                    ->with('success', view('flash/success', ['message' => __('admin.types.alert.update.success', ['name_singular' => $type->name_singular, 'name_plural' => $type->name_plural])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/types/update', [
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

        if (null === $type = \App\Models\Type::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $type->deleted = 1;
        $type->save();

        return redirect(adminRoute('types', session()->get('admin/types')))
            ->with('success', view('flash/success', ['message' => __('admin.types.alert.remove.success', ['name_singular' => $type->name_singular, 'name_plural' => $type->name_plural])]));
    }


    private function getForm($model)
    {
        return form($model)
            ->add('active', 'toggle', ['label' => __('admin.types.form.label.active'), 'value' => 1])
            ->add('type', 'select', ['label' => __('admin.types.form.label.type'), 'options' => $this->types])
            ->add('name_singular', 'translatable', ['label' => __('admin.types.form.label.name_singular'), 'constraints' => 'transrequired'])
            ->add('name_plural', 'translatable', ['label' => __('admin.types.form.label.name_plural'), 'constraints' => 'transrequired'])
            ->add('slug', 'text', ['label' => __('admin.types.form.label.slug'), 'sluggable' => 'name_plural', 'constraints' => 'required|alphanumericdash|maxlength:120'])
            ->add('icon', 'icon', ['label' => __('admin.types.form.label.icon')])
            ->add('localizable', 'toggle', ['label' => __('admin.types.form.label.localizable'), 'value' => 1])
            ->add('reviewable', 'toggle', ['label' => __('admin.types.form.label.reviewable'), 'value' => 1])
            ->add('peruser_limit', 'number', ['label' => __('admin.types.form.label.peruser_limit'), 'value' => 0, 'constraints' => 'required|min:0'])
            ->add('parents', 'tree', [
                'label' => __('admin.types.form.label.parents'), 
                'tree_source' => (new \App\Models\Type())->getTree(),
            ])
            ->add('ratings', 'tree', [
                'label' => __('admin.types.form.label.ratings'), 
                'tree_source' => (new \App\Models\Rating())->getTree(),
            ])
            ->add('address_format', 'textarea', ['label' => __('admin.types.form.label.address_format'), 'value' => '<span itemprop="streetAddress">{address}</span>
<span itemprop="addressLocality">{location_3}</span>, <span itemprop="addressRegion">{location_2}</span> <span itemprop="postalCode">{zip}</span> <span itemprop="addressCountry">{location_1}</span>', 'constraints' => 'required'])
            ->add('approvable', 'toggle', ['label' => __('admin.types.form.label.approvable')])
            ->add('approvable_updates', 'toggle', ['label' => __('admin.types.form.label.approvable_updates')])
            ->add('approvable_reviews', 'toggle', ['label' => __('admin.types.form.label.approvable_reviews')])
            ->add('approvable_comments', 'toggle', ['label' => __('admin.types.form.label.approvable_comments')])
            ->add('approvable_messages', 'toggle', ['label' => __('admin.types.form.label.approvable_messages')])
            ->add('approvable_replies', 'toggle', ['label' => __('admin.types.form.label.approvable_replies')]);
    }

}
