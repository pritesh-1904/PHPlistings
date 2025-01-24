<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class WidgetFields
    extends \App\Controllers\Admin\BaseController
{

    public $types;

    public function __construct()
    {
        parent::__construct();

        $this->types = [
            'captcha' => __('admin.widgetfields.type.captcha'),
            'checkbox' => __('admin.widgetfields.type.checkbox'),
            'color' => __('admin.widgetfields.type.color'),
            'date' => __('admin.widgetfields.type.date'),
            'dates' => __('admin.widgetfields.type.dates'),
            'datetime' => __('admin.widgetfields.type.datetime'),
            'email' => __('admin.widgetfields.type.email'),
            'hidden' => __('admin.widgetfields.type.hidden'),
            'hours' => __('admin.widgetfields.type.hours'),
            'htmltextarea' => __('admin.widgetfields.type.htmltextarea'),
            'keywords' => __('admin.widgetfields.type.keywords'),
            'locationmappicker' => __('admin.widgetfields.type.locationmappicker'),
            'mselect' => __('admin.widgetfields.type.mselect'),
            'number' => __('admin.widgetfields.type.number'),
            'password' => __('admin.widgetfields.type.password'),
            'phone' => __('admin.widgetfields.type.phone'),
            'price' => __('admin.widgetfields.type.price'),
            'radio' => __('admin.widgetfields.type.radio'),
            'rating' => __('admin.widgetfields.type.rating'),
            'ro' => __('admin.widgetfields.type.readonly'),
            'select' => __('admin.widgetfields.type.select'),
            'separator' => __('admin.widgetfields.type.separator'),
            'text' => __('admin.widgetfields.type.text'),
            'textarea' => __('admin.widgetfields.type.textarea'),
            'time' => __('admin.widgetfields.type.time'),
            'timezone' => __('admin.widgetfields.type.timezone'),
            'toggle' => __('admin.widgetfields.type.toggle'),
            'url' => __('admin.widgetfields.type.url'),
            'youtube' => __('admin.widgetfields.type.youtube'),
        ];
    }

    public function actionIndex($params)
    {
        if (!auth()->check('admin_appearance')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\WidgetFieldGroup::find($params['group'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.widgetfields.title.index'));

        $fields = \App\Models\WidgetField::search(null, [], 'admin/widget-fields/' . $group->id)
            ->where('widgetfieldgroup_id', $group->id)
            ->orderBy('weight')
            ->paginate();

        $table = dataTable($fields)
            ->addColumns([
                'id' => [__('admin.widgetfields.datatable.label.id')],
                'label' => [__('admin.widgetfields.datatable.label.label')],
                'type' => [__('admin.widgetfields.datatable.label.type'), function ($field) {
                    return $this->types[$field->type];
                }],
            ])
            ->addActions([
                'edit' => [__('admin.widgetfields.datatable.action.edit'), function ($field) use ($group) {
                    return adminRoute('widget-fields/' . $group->id . '/update/' . $field->id);
                }],
                'constraints' => [__('admin.widgetfields.datatable.action.constraints'), function ($field) use ($group) {
                    return adminRoute('widget-field-constraints/' . $group->id , ['field_id' => $field->id]);
                }],
                'options' => [__('admin.widgetfields.datatable.action.options'), function ($field) use ($group) {
                    if (in_array($field->type, ['checkbox', 'mselect', 'select', 'radio'])) {
                        return adminRoute('widget-field-options/' . $group->id , ['field_id' => $field->id]);
                    }
                }],
                'delete' => ['Delete', function ($field) use ($group) {
                    if (null !== $field->customizable) {
                        return adminRoute('widget-fields/' . $group->id . '/delete/' . $field->id);
                    }
                }],
            ])
            ->setSortable('widget-fields');

        return response(layout()->content(
            view('admin/widget-fields/index', [
                'group' => $group,
                'fields' => $table,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (!auth()->check('admin_appearance')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\WidgetFieldGroup::find($params['group'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.widgetfields.title.index'));

        $field = new \App\Models\WidgetField();

        $form = $this->getForm($field)
            ->remove('value')
            ->add('submit', 'submit', ['label' => __('admin.widgetfields.form.label.submit')])
            ->handleRequest();
    
        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                if ($group->fields()->where('name', $form->getValues()->name)->count() > 0) {
                    $form->setValidationError('name', __('form.validation.unique'));
                }
            }
            
            if ($form->isValid()) {
                $field->customizable = 1;
                
                $group->fields()->save($field);

                if (null !== $form->getValues()->required) {
                    $constraint = new \App\Models\WidgetFieldConstraint(['name' => ($input->type == 'dropzone' ? 'filerequired' : 'required')]);
                    $field->constraints()->save($constraint);
                }

                return redirect(adminRoute('widget-fields/' . $group->id, session()->get('admin/widget-fields/' . $group->id)))
                    ->with('success', view('flash/success', ['message' => __('admin.widgetfields.alert.create.success', ['name' => $field->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/widget-fields/create', [
                'group' => $group,
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

        if (null === $group = \App\Models\WidgetFieldGroup::find($params['group'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $field = \App\Models\WidgetField::where('id', $params['id'])->where('widgetfieldgroup_id', $group->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.widgetfields.title.update'));

        $form = $this->getForm($field)
            ->remove('required')
            ->add('submit', 'submit', ['label' => __('admin.widgetfields.form.label.update')])
            ->handleRequest();

        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                if ($group->fields()->where('id', '!=', $field->id)->where('name', $form->getValues()->name)->count() > 0) {
                    $form->setValidationError('name', __('form.validation.unique'));
                }
            }

            if ($form->isValid()) {
                $field->save();

                return redirect(adminRoute('widget-fields/' . $group->id, session()->get('admin/widget-fields/' . $group->id)))
                    ->with('success', view('flash/success', ['message' => __('admin.widgetfields.alert.update.success', ['name' => $field->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/widget-fields/update', [
                'group' => $group,
                'form' => $form,
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

        if (null === $group = \App\Models\WidgetFieldGroup::find($params['group'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $field = \App\Models\WidgetField::where('id', $params['id'])->where('widgetfieldgroup_id', $group->id)->whereNotNull('customizable')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $field->delete();

        return redirect(adminRoute('widget-fields/' . $group->id, session()->get('admin/widget-fields/' . $group->id)))
            ->with('success', view('flash/success', ['message' => __('admin.widgetfields.alert.remove.success', ['name' => $field->name])]));
    }

    private function getForm($model)
    {
        $form = form($model)
            ->add('required', 'toggle', ['label' => __('admin.widgetfields.form.label.required')])
            ->add('type', 'select', ['label' => __('admin.widgetfields.form.label.type'), 'options' => $this->types])
            ->add('label', 'translatable', ['label' => __('admin.widgetfields.form.label.label')])
            ->add('name', 'text', ['label' => __('admin.widgetfields.form.label.name'), 'constraints' => 'required|alphanumeric|maxlength:120']);

        switch ($model->type) {
            case 'checkbox':
            case 'mselect':
                $form->add('value', 'textarea', ['label' => __('admin.widgetfields.form.label.value')]);
                break;
            case 'hidden':
            case 'password':
            case 'radio':
            case 'ro':
            case 'select':
                $form->add('value', 'text', ['label' => __('admin.widgetfields.form.label.value')]);
                break;
            case 'color':
            case 'date':
            case 'dates':
            case 'datetime':
            case 'email':
            case 'htmltextarea':
            case 'keywords':
            case 'number':
            case 'phone':
            case 'price':
            case 'rating':
            case 'text':
            case 'textarea':
            case 'time':
            case 'timezone':
            case 'toggle':
            case 'url':
            case 'youtube':
                $form->add('value', $model->type, ['label' => __('admin.widgetfields.form.label.value')]);
                break;
            default:
        }
            
        $form
            ->add('placeholder', 'translatable', ['label' => __('admin.widgetfields.form.label.placeholder')])
            ->add('description', 'translatable', ['label' => __('admin.widgetfields.form.label.description')]);

        return $form;
    }

}
