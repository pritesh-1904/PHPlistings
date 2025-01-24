<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class EmailTemplates
    extends \App\Controllers\Admin\BaseController
{

    public $priority;

    public function __construct()
    {
        parent::__construct();

        $this->priority = [
            '0' => __('admin.emailtemplates.form.label.priority.0'),
            '1' => __('admin.emailtemplates.form.label.priority.1'),
            '2' => __('admin.emailtemplates.form.label.priority.2'),
            '3' => __('admin.emailtemplates.form.label.priority.3'),
            '4' => __('admin.emailtemplates.form.label.priority.4'),
            '5' => __('admin.emailtemplates.form.label.priority.5'),
        ];    
    }

    public function actionIndex($params)
    {
        if (!auth()->check('admin_emails')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.emailtemplates.title.index'));

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'asc';
        }

        $templates = \App\Models\EmailTemplate::search(null, [], 'admin/email-templates')
            ->paginate();

        $table = dataTable($templates)
            ->addColumns([
                'id' => [__('admin.emailtemplates.datatable.label.id')],
                'name' => [__('admin.emailtemplates.datatable.label.name')],
                'active' => [__('admin.emailtemplates.datatable.label.active'), function ($template) {
                    return view('misc/ajax-switch', [
                        'table' => 'emailtemplates',
                        'column' => 'active',
                        'id' => $template->id,
                        'value' => $template->active,
                    ]);
                }],
                'moderatable' => [__('admin.emailtemplates.datatable.label.moderatable'), function ($template) {
                    return view('misc/ajax-switch', [
                        'table' => 'emailtemplates',
                        'column' => 'moderatable',
                        'id' => $template->id,
                        'value' => $template->moderatable,
                    ]);
                }],
            ])
            ->addActions([
                'edit' => [__('admin.emailtemplates.datatable.action.edit'), function ($template) {
                    return adminRoute('email-templates/update/' . $template->id);
                }],
                'delete' => [__('admin.emailtemplates.datatable.action.delete'), function ($template) {
                    if (null === $template->customizable) {
                        return null;
                    }
                    
                    return adminRoute('email-templates/delete/' . $template->id);
                }],
            ])
            ->orderColumns([
                'id',
                'name',
            ]);

        return response(layout()->content(
            view('admin/email-templates/index', [
                'templates' => $table,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (!auth()->check('admin_emails')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.emailtemplates.title.create'));

        $template = new \App\Models\EmailTemplate;
        
        $form = $this->getForm($template)
            ->add('submit', 'submit', ['label' => __('admin.emailtemplates.form.label.submit')])
            ->handleRequest();
    
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $template->customizable = 1;
                $template->save();

                return redirect(adminRoute('email-templates', session()->get('admin/email-templates')))
                    ->with('success', view('flash/success', ['message' => __('admin.emailtemplates.alert.create.success', ['name' => $template->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/email-templates/create', [
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionUpdate($params)
    {
        if (!auth()->check('admin_emails')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $template = \App\Models\EmailTemplate::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.emailtemplates.title.update'));

        $form = $this->getForm($template)
            ->add('submit', 'submit', ['label' => __('admin.emailtemplates.form.label.update')]);

        if (null === $template->customizable) {
            $form->remove('name');
        }

        $form->handleRequest();

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $template->save();

                return redirect(adminRoute('email-templates', session()->get('admin/email-templates')))
                    ->with('success', view('flash/success', ['message' => __('admin.emailtemplates.alert.update.success', ['name' => $template->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/email-templates/update', [
                'form' => $form,
                'alert' => $alert ?? null,
                'template' => $template,
            ])
        ));
    }

    public function actionDelete($params)
    {
        if (!auth()->check('admin_emails')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $template = \App\Models\EmailTemplate::where('id', $params['id'])->whereNotNull('customizable')) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $template->delete();

        return redirect(adminRoute('email-templates', session()->get('admin/email-templates')))
            ->with('success', view('flash/success', ['message' => __('admin.emailtemplates.alert.remove.success', ['name' => $template->name])]));
    }

    private function getForm($model)
    {
        return form($model)
            ->add('active', 'toggle', ['label' => __('admin.emailtemplates.form.label.active'), 'value' => 1])
            ->add('moderatable', 'toggle', ['label' => __('admin.emailtemplates.form.label.moderatable')])
            ->add('priority', 'select', ['label' => __('admin.emailtemplates.form.label.priority'), 'options' => $this->priority])
            ->add('name', 'text', ['label' => __('admin.emailtemplates.form.label.name'), 'constraints' => 'required|alphanumericdash|maxlength:120|unique:emailtemplates,name' . (null !== $model->get($model->getPrimaryKey()) ? ',' . $model->get($model->getPrimaryKey()) : '')])
            ->add('from_email', 'email', ['label' => __('admin.emailtemplates.form.label.from_email')])
            ->add('from_name', 'text', ['label' => __('admin.emailtemplates.form.label.from_name')])
            ->add('to_email', 'email', ['label' => __('admin.emailtemplates.form.label.to_email')])
            ->add('to_name', 'text', ['label' => __('admin.emailtemplates.form.label.to_name')])
            ->add('reply_to', 'text', ['label' => __('admin.emailtemplates.form.label.reply_to')])
            ->add('subject', 'text', ['label' => __('admin.emailtemplates.form.label.subject')])
            ->add('body', 'htmltextarea', ['label' => __('admin.emailtemplates.form.label.body'), 'config' => 'advanced', 'constraints' => 'required']);
    }

}
