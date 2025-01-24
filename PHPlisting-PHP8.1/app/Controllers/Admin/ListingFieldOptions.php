<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class ListingFieldOptions
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (false === auth()->check(['admin_content', 'admin_fields'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\ListingFieldGroup::where('slug', $params['group'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (
            ('listings' == $group->slug && false === auth()->check('admin_listings')) || 
            ('messages' == $group->slug && false === auth()->check('admin_messages')) ||
            ('reviews' == $group->slug && false === auth()->check('admin_reviews'))
        ) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === request()->get->get('field_id') || null === $field = $type->fields()->where('id', request()->get->field_id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.listingfieldoptions.' . $group->slug . '.title.index', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $options = $field
            ->options()
            ->orderBy('weight')
            ->paginate();

        $table = dataTable($options)
            ->addColumns([
                'name' => [__('admin.listingfieldoptions.datatable.label.name')],
                'value' => [__('admin.listingfieldoptions.datatable.label.value')],
            ])
            ->addActions([
                'edit' => [__('admin.listingfieldoptions.datatable.action.edit'), function ($option) use ($group, $type, $field) {
                    return adminRoute($group->slug . '-field-options/' . $type->slug . '/update/' . $option->id, ['field_id' => $field->id]);
                }],
                'delete' => [__('admin.fieldoptions.datatable.action.delete'), function ($option) use ($group, $type, $field) {
                    if (null === $option->customizable) {
                        return null;
                    }

                    return adminRoute($group->slug . '-field-options/' . $type->slug . '/delete/' . $option->id, ['field_id' => $field->id]);
                }],
            ])
            ->setSortable('listing-field-options');

        return response(layout()->content(
            view('admin/listing-field-options/index', [
                'group' => $group,
                'type' => $type,
                'field' => $field,
                'options' => $table,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (false === auth()->check(['admin_content', 'admin_fields'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\ListingFieldGroup::where('slug', $params['group'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (
            ('listings' == $group->slug && false === auth()->check('admin_listings')) || 
            ('messages' == $group->slug && false === auth()->check('admin_messages')) ||
            ('reviews' == $group->slug && false === auth()->check('admin_reviews'))
        ) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === request()->get->get('field_id') || null === $field = $type->fields()->where('id', request()->get->field_id)->whereNotNull('customizable')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.listingfieldoptions.' . $group->slug . '.title.index', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $option = new \App\Models\ListingFieldOption();

        $form = form($option)
            ->add('name', 'text', ['label' => __('admin.listingfieldoptions.form.label.name'), 'constraints' => 'required|alphanumeric'])
            ->add('value', 'translatable', ['label' => __('admin.listingfieldoptions.form.label.value'), 'constraints' => 'transrequired'])
            ->add('submit', 'submit', ['label' => __('admin.listingfieldoptions.form.label.submit')])
            ->handleRequest();

        if ($form->isSubmitted()) {           
            if ($form->isValid()) {
                $option->customizable = 1;
                $field->options()->save($option);

                return redirect(adminRoute($group->slug . '-field-options/' . $type->slug, ['field_id' => $field->id]))
                    ->with('success', view('flash/success', ['message' => __('admin.listingfieldoptions.alert.create.success', ['name' => $option->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/listing-field-options/create', [
                'group' => $group,
                'type' => $type,
                'field' => $field,
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionUpdate($params)
    {
        if (false === auth()->check(['admin_content', 'admin_fields'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\ListingFieldGroup::where('slug', $params['group'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (
            ('listings' == $group->slug && false === auth()->check('admin_listings')) || 
            ('messages' == $group->slug && false === auth()->check('admin_messages')) ||
            ('reviews' == $group->slug && false === auth()->check('admin_reviews'))
        ) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === request()->get->get('field_id') || null === $field = $type->fields()->where('id', request()->get->field_id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $option = $field->options()->where('id', $params['id'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.listingfieldoptions.' . $group->slug . '.title.update', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $form = form($option)
            ->add('name', 'text', ['label' => __('admin.listingfieldoptions.form.label.name'), 'constraints' => 'required|alphanumeric'])
            ->add('value', 'translatable', ['label' => __('admin.listingfieldoptions.form.label.value'), 'constraints' => 'transrequired'])
            ->add('submit', 'submit', ['label' => __('admin.listingfieldoptions.form.label.update')]);

        if (null === $option->customizable) {
            $form->remove('name');
        }

        $form->handleRequest();

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $field->options()->save($option);

                return redirect(adminRoute($group->slug . '-field-options/' . $type->slug, ['field_id' => $field->id]))
                    ->with('success', view('flash/success', ['message' => __('admin.listingfieldoptions.alert.update.success', ['name' => $option->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/listing-field-options/update', [
                'group' => $group,
                'type' => $type,
                'field' => $field,
                'option' => $option,
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionDelete($params)
    {
        if (false === auth()->check(['admin_content', 'admin_fields'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\ListingFieldGroup::where('slug', $params['group'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (
            ('listings' == $group->slug && false === auth()->check('admin_listings')) || 
            ('messages' == $group->slug && false === auth()->check('admin_messages')) ||
            ('reviews' == $group->slug && false === auth()->check('admin_reviews'))
        ) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === request()->get->get('field_id') || null === $field = $type->fields()->where('id', request()->get->field_id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $option = $field->options()->where('id', $params['id'])->whereNotNull('customizable')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $option->delete();

        return redirect(adminRoute($group->slug . '-field-options/' . $type->slug, ['field_id' => $field->id]))
            ->with('success', view('flash/success', ['message' => __('admin.listingfieldoptions.alert.remove.success', ['name' => $option->name])]));
    }

    public function actionCreateMultiple($params)
    {
        if (false === auth()->check(['admin_content', 'admin_fields'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\ListingFieldGroup::where('slug', $params['group'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (
            ('listings' == $group->slug && false === auth()->check('admin_listings')) || 
            ('messages' == $group->slug && false === auth()->check('admin_messages')) ||
            ('reviews' == $group->slug && false === auth()->check('admin_reviews'))
        ) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === request()->get->get('field_id') || null === $field = $type->fields()->where('id', request()->get->field_id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.listingfieldoptions.' . $group->slug . '.title.create_multiple', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $form = form()
            ->add('language_id', 'select', [
                'label' => __('admin.listingfieldoptions.form.label.language'),
                'options' => \App\Models\Language::whereNotNull('active')->get()->pluck('name', 'id')->all(),
                'constraints' => 'required',
            ])
            ->add('csv', 'textarea', ['label' => __('admin.listingfieldoptions.form.label.csv'), 'placeholder' => 'e.g.:
airconditioning;Air-Conditioning
Outdoor Tennis Court
WiFi', 'constraints' => 'required'])
            ->add('submit', 'submit', ['label' => __('admin.listingfieldoptions.form.label.submit')])
            ->handleRequest();

        if ($form->isSubmitted()) {           
            $input = $form->getValues();

            $language = \App\Models\Language::find($input->get('language_id'));

            if ($form->isValid()) {
                $strings = explode('<br />', \nl2br($input->csv));
                
                foreach ($strings as $string) {
                    if ('' == trim($string)) {
                        continue;
                    }

                    $items = explode(';', d($string));

                    if (1 == count($items) || 2 == count($items)) {
                        if ('' != trim($items[0])) {
                            $option = new \App\Models\ListingFieldOption();
                            $option->customizable = 1;
                            $option->name = trim(str_replace('-', '', slugify($items[0])));

                            if (1 == count($items)) {
                                $value = trim(e($items[0]));
                            } else {
                                $value = trim(e($items[1]));
                            }

                            $option->setTranslation('value', $value, config()->app->locale_fallback);

                            if (config()->app->locale_fallback != $language->locale) {
                                $option->setTranslation('value', $value, $language->locale);
                            }
                        }

                        $field->options()->save($option);
                    }
                }

                return redirect(adminRoute($group->slug . '-field-options/' . $type->slug, ['field_id' => $field->id]))
                    ->with('success', view('flash/success', ['message' => __('admin.listingfieldoptions.alert.create.success', ['name' => 'Multiple'])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/listing-field-options/create-multiple', [
                'group' => $group,
                'type' => $type,
                'field' => $field,
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

}
