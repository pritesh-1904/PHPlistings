<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Fields
    extends \App\Controllers\Admin\BaseController
{

    public $types;

    public function __construct()
    {
        parent::__construct();

        $this->types = [
            'captcha' => __('admin.fields.type.captcha'),
            'checkbox' => __('admin.fields.type.checkbox'),
            'color' => __('admin.fields.type.color'),
            'date' => __('admin.fields.type.date'),
            'dates' => __('admin.fields.type.dates'),
            'datetime' => __('admin.fields.type.datetime'),
            'dropzone' => __('admin.fields.type.file'),
            'email' => __('admin.fields.type.email'),
            'hidden' => __('admin.fields.type.hidden'),
            'hours' => __('admin.fields.type.hours'),
            'htmltextarea' => __('admin.fields.type.htmltextarea'),
            'keywords' => __('admin.fields.type.keywords'),
            'locationmappicker' => __('admin.fields.type.locationmappicker'),
            'mselect' => __('admin.fields.type.mselect'),
            'number' => __('admin.fields.type.number'),
            'password' => __('admin.fields.type.password'),
            'phone' => __('admin.fields.type.phone'),
            'price' => __('admin.fields.type.price'),
            'radio' => __('admin.fields.type.radio'),
            'rating' => __('admin.fields.type.rating'),
            'ro' => __('admin.fields.type.readonly'),
            'select' => __('admin.fields.type.select'),
            'separator' => __('admin.fields.type.separator'),
            'text' => __('admin.fields.type.text'),
            'textarea' => __('admin.fields.type.textarea'),
            'time' => __('admin.fields.type.time'),
            'timezone' => __('admin.fields.type.timezone'),
            'toggle' => __('admin.fields.type.toggle'),
            'url' => __('admin.fields.type.url'),
            'youtube' => __('admin.fields.type.youtube'),
        ];
    }

    public function actionIndex($params)
    {
        if (!auth()->check('admin_fields')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\FieldGroup::where('slug', $params['group'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ('users' == $group->slug && !auth()->check('admin_users')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.fields.' . $group->slug . '.title.index'));

        $fields = \App\Models\Field::search(null, [], 'admin/fields/' . $group->slug)
            ->where('fieldgroup_id', $group->id)
            ->orderBy('weight')
            ->paginate();

        $table = dataTable($fields)
            ->addColumns([
                'label' => [__('admin.fields.datatable.label.label')],
                'type' => [__('admin.fields.datatable.label.type'), function ($field) {
                    return $this->types[$field->type];
                }],
            ])
            ->addActions([
                'edit' => [__('admin.fields.datatable.action.edit'), function ($field) use ($group) {
                    return adminRoute('fields/' . $group->slug . '/update/' . $field->id);
                }],
                'constraints' => [__('admin.fields.datatable.action.constraints'), function ($field) use ($group) {
                    return adminRoute('field-constraints/' . $group->slug, ['field_id' => $field->id]);
                }],
                'options' => [__('admin.fields.datatable.action.options'), function ($field) use ($group) {
                    if (in_array($field->type, ['checkbox', 'mselect', 'select', 'radio'])) {
                        return adminRoute('field-options/' . $group->slug, ['field_id' => $field->id]);
                    }
                }],
                'delete' => [__('admin.fields.datatable.action.delete'), function ($field) use ($group) {
                    if (null !== $field->customizable) {
                        return adminRoute('fields/' . $group->slug . '/delete/' . $field->id);
                    }
                }],
            ])
            ->setSortable('fields');

        return response(layout()->content(
            view('admin/fields/index', [
                'group' => $group,
                'fields' => $table,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (!auth()->check('admin_fields')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\FieldGroup::where('slug', $params['group'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ('users' == $group->slug && !auth()->check('admin_users')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.fields.' . $group->slug . '.title.create'));

        layout()->addFooterJs('
        <script>
            $(document).ready(function() {
                var uploadidElement = $(\'#upload_id\').closest(\'div[class*="form-group"]\');

                if ($.inArray($("#type").val(), ["dropzone"]) === -1) {
                    uploadidElement.hide();
                }

                $("#type").on("change", function() {
                    if ($.inArray($(this).val(), ["dropzone"]) === -1) {
                        uploadidElement.slideUp("slow");
                    } else {
                        uploadidElement.slideDown("slow");
                    }
                });
            });
        </script>
        ');

        unset(
            $this->types['locationmappicker'], 
            $this->types['password'], 
            $this->types['ro'], 
            $this->types['hidden']
        );

        $field = new \App\Models\Field();

        $form = form($field)
            ->add('submittable', 'toggle', ['label' => __('admin.fields.form.label.submittable'), 'value' => '1'])
            ->add('updatable', 'toggle', ['label' => __('admin.fields.form.label.updatable'), 'value' => '1'])
            ->add('outputable', 'toggle', ['label' => __('admin.fields.form.label.outputable')])
            ->add('required', 'toggle', ['label' => __('admin.fields.form.label.required')])
            ->add('type', 'select', ['label' => __('admin.fields.form.label.type'), 'options' => $this->types])
            ->add('upload_id', 'select', ['label' => __('admin.fields.form.label.upload_type'), 'options' => \App\Models\UploadType::whereNotNull('customizable')->get()->pluck('name', 'id')->all()])
            ->add('label', 'translatable', ['label' => __('admin.fields.form.label.label')])
            ->add('name', 'text', ['label' => __('admin.fields.form.label.name'), 'constraints' => 'required|alphanumeric|maxlength:120'])
            ->add('placeholder', 'translatable', ['label' => __('admin.fields.form.label.placeholder')])
            ->add('description', 'translatable', ['label' => __('admin.fields.form.label.description')])
            ->add('submit', 'submit', ['label' => __('admin.fields.form.label.submit')])
            ->handleRequest();
    
        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                if ($group->fields()->where('name', $group->slug . '_' . $input->name)->count() > 0) {
                    $form->setValidationError('name', __('form.validation.unique'));
                }
            }

            if ($form->isValid()) {
                $field->name = $group->slug . '_' . $input->name;
                $field->customizable = 1;
                $group->fields()->save($field);

                if (null !== $input->required) {
                    $constraint = new \App\Models\FieldConstraint(['name' => ($input->type == 'dropzone' ? 'filerequired' : 'required')]);
                    $constraint->customizable = 1;
                    $field->constraints()->save($constraint);
                }

                return redirect(adminRoute('fields/' . $group->slug, session()->get('admin/fields/' . $group->slug)))
                    ->with('success', view('flash/success', ['message' => __('admin.fields.alert.create.success', ['name' => $field->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/fields/create', [
                'group' => $group,
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionUpdate($params)
    {
        if (!auth()->check('admin_fields')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\FieldGroup::where('slug', $params['group'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ('users' == $group->slug && !auth()->check('admin_users')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $field = $group->fields()->where('id', $params['id'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.fields.' . $group->slug . '.title.update'));

        $form = form($field);

        if (null !== $field->customizable) {
            $form
                ->add('submittable', 'toggle', ['label' => __('admin.fields.form.label.submittable')])
                ->add('updatable', 'toggle', ['label' => __('admin.fields.form.label.updatable')])
                ->add('outputable', 'toggle', ['label' => __('admin.fields.form.label.outputable')]);
        }

        if (null !== $field->customizable && $field->type == 'dropzone') {
            $form->add('upload_id', 'select', ['label' => __('admin.fields.form.label.upload_type'), 'options' => \App\Models\UploadType::whereNotNull('public')->get()->pluck('name', 'id')->all()]);
        }

        $form->add('label', 'translatable', ['label' => __('admin.fields.form.label.label')]);

        switch ($field->type) {
            case 'checkbox':
            case 'mselect':
                $form->add('value', 'textarea', ['label' => __('admin.fields.form.label.value')]);
                break;
            case 'hidden':
            case 'password':
            case 'radio':
            case 'ro':
            case 'select':
                $form->add('value', 'text', ['label' => __('admin.fields.form.label.value')]);
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
                $form->add('value', $field->type, ['label' => __('admin.fields.form.label.value')]);
                break;
            default:
        }

        $form
            ->add('placeholder', 'translatable', ['label' => __('admin.fields.form.label.placeholder')])
            ->add('description', 'translatable', ['label' => __('admin.fields.form.label.description')])
            ->add('submit', 'submit', ['label' => __('admin.fields.form.label.update')])
            ->handleRequest();

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $field->save();

                return redirect(adminRoute('fields/' . $group->slug, session()->get('admin/fields/' . $group->slug)))
                    ->with('success', view('flash/success', ['message' => __('admin.fields.alert.update.success', ['name' => $field->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/fields/update', [
                'group' => $group,
                'form' => $form,
                'alert' => $alert ?? null,
                'field' => $field,
            ])
        ));
    }

    public function actionDelete($params)
    {
        if (!auth()->check('admin_users')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\FieldGroup::where('slug', $params['group'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $field = $group->fields()->where('id', $params['id'])->whereNotNull('customizable')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $field->delete();

        return redirect(adminRoute('fields/' . $group->slug, session()->get('admin/fields/' . $group->slug)))
            ->with('success', view('flash/success', ['message' => __('admin.fields.alert.remove.success', ['name' => $field->name])]));
    }

}
