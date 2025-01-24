<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Locations
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check('admin_locations')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if ('' == request()->get->get('parent_id', '')) {
            request()->get->parent_id = (new \App\Models\Location)->getRoot()->id;
        }

        if ('' != request()->get->get('location_id', '')) {
            request()->get->parent_id = '';
        }

        layout()->setTitle(__('admin.locations.title.index'));

        if (null === request()->get->get('sort')) {
            request()->get->sort = '_left';
            request()->get->sort_direction = 'asc';
        }

        $locations = \App\Models\Location::search(
                (new \App\Models\Location())
                    ->setSortable('_left', '_left', null)
                    ->setSortable('id', 'id', null)
                    ->setSortable('slug', 'slug', null)
                    ->setSortable('impressions', 'impressions', null),
                [],
                'admin/locations'
            )
            ->paginate();

        $table = dataTable($locations)
            ->addColumns([
                'id' => [__('admin.locations.datatable.label.id')],
                'name' => [__('admin.locations.datatable.label.name')],
                'slug' => [__('admin.locations.datatable.label.slug')],
                'impressions' => [__('admin.locations.datatable.label.impressions'), function ($location) {
                    return $location->get('impressions', 0);
                }],
/*
                'active' => [__('admin.locations.datatable.label.published'), function ($location) {
                    return view('misc/ajax-switch', [
                        'table' => 'locations',
                        'column' => 'active',
                        'id' => $location->id,
                        'value' => $location->active
                    ]);
                }],
*/
                'featured' => [__('admin.locations.datatable.label.featured'), function ($location) {
                    return view('misc/ajax-switch', [
                        'table' => 'locations',
                        'column' => 'featured',
                        'id' => $location->id,
                        'value' => $location->featured
                    ]);
                }],

            ])
            ->orderColumns([
                'id',
                'slug',
                'impressions',
            ])
            ->addActions([
                'edit' => [__('admin.locations.datatable.action.edit'), function ($location) {
                    return adminRoute('locations/update/' . $location->id);
                }],
                'children' => [__('admin.locations.datatable.action.children'), function ($location) {
                    if (false === $location->isLeaf()) {
                        return adminRoute('locations', ['parent_id' => $location->id]);
                    }
                }],
                'delete' => [__('admin.locations.datatable.action.delete'), function ($location) {
                    if (false !== $location->isLeaf()) {
                        return adminRoute('locations/delete/' . $location->id);
                    }
                }],
            ]);

        $form = form()
            ->setMethod('get')
            ->setTemplate('form/inline')
            ->add('location_id', 'location', [
                'placeholder' => __('admin.locations.searchform.label.location'),
                'weight' => 10
            ])
            ->add('submit', 'submit', [
                'label' => __('admin.locations.searchform.label.submit')
            ])
            ->forceRequest();

        return response(layout()->content(
            view('admin/locations/index', [
                'locations' => $table,
                'parent' => \App\Models\Location::find(request()->get->parent_id),
                'form' => $form,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (!auth()->check('admin_locations')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (!isset(request()->get->parent_id)) {
            request()->get->parent_id = (new \App\Models\Location)->getRoot()->id;
        }

        layout()->setTitle(__('admin.locations.title.create'));

        $location = new \App\Models\Location();

        $form = $this->getForm($location)
            ->add('submit', 'submit', ['label' => __('admin.locations.form.label.submit')])
            ->handleRequest();
        
        if ($form->isSubmitted()) {           
            $input = $form->getValues();

            $root = (new \App\Models\Location)->getRoot();

            if (null !== $input->get('location') && $root->id == $input->location) {
                $input->location = null;
            }

            if ($input->placement != 'root' && null === \App\Models\Location::find($input->get('location'))) {
                $form->setValidationError('location', __('form.validation.required'));
            }

            if ($form->isValid()) {
                switch($input->placement) {
                    case 'root':
                        $location->appendTo($root);
                        break;
                    case 'append':
                        $location->appendTo(\App\Models\Location::find($input->location));
                        break;
                    case 'before':
                        $location->insertBefore(\App\Models\Location::find($input->location));
                        break;
                    case 'after':
                        $location->insertAfter(\App\Models\Location::find($input->location));
                        break;
                }

                $location->active = 1;
                $location->save();

                return redirect(adminRoute('locations', session()->get('admin/locations')))
                    ->with('success', view('flash/success', ['message' => __('admin.locations.alert.create.success', ['id' => $location->id, 'name' => $location->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/locations/create', [
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionUpdate($params)
    {
        if (!auth()->check('admin_locations')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $location = \App\Models\Location::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.locations.title.update'));

        $form = $this->getForm($location)
            ->add('submit', 'submit', ['label' => __('admin.locations.form.label.update')])
            ->removeConstraints('placement')
            ->handleRequest();

        if ($form->isSubmitted()) {           
            $input = $form->getValues();

            $root = (new \App\Models\Location)->getRoot();

            if (null !== $input->get('location') && $root->id == $input->location) {
                $input->location = null;
            }

            if (null !== $input->get('placement') && $input->get('placement') != 'root' && null === \App\Models\Location::find($input->get('location'))) {
                $form->setValidationError('location', __('form.validation.required'));
            }

            if ($form->isValid()) {
                switch($input->placement) {
                    case 'root':
                        $location->appendTo($root);
                        break;
                    case 'append':
                        $location->appendTo(\App\Models\Location::find($input->location));
                        break;
                    case 'before':
                        $location->insertBefore(\App\Models\Location::find($input->location));
                        break;
                    case 'after':
                        $location->insertAfter(\App\Models\Location::find($input->location));
                        break;
                }

                $location->active = 1;
                $location->save();

                return redirect(adminRoute('locations', session()->get('admin/locations')))
                    ->with('success', view('flash/success', ['message' => __('admin.locations.alert.update.success', ['id' => $location->id, 'name' => $location->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/locations/update', [
                'form' => $form,
                'alert' => $alert ?? null,
                'location' => $location,
            ])
        ));
    }

    public function actionDelete($params)
    {
        if (!auth()->check('admin_locations')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $location = \App\Models\Location::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (false === $location->isRoot() && false !== $location->isLeaf()) {
            if ($location->listings()->count() > 0 || $location->users()->count() > 0) {
                return redirect(adminRoute('locations', session()->get('admin/locations')))
                    ->with('error', view('flash/error', ['message' => __('admin.locations.alert.remove.failed', ['id' => $location->id, 'name' => $location->name])]));
            }
            
            $location->delete();
        }

        return redirect(adminRoute('locations', session()->get('admin/locations')))
            ->with('success', view('flash/success', ['message' => __('admin.locations.alert.remove.success', ['id' => $location->id, 'name' => $location->name])]));
    }

    public function actionCreateMultiple($params)
    {
        if (!auth()->check('admin_locations')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.locations.title.create_multiple'));

        $form = form()
//            ->add('active', 'toggle', ['label' => __('admin.locations.form.label.published'), 'value' => 1])
            ->add('language_id', 'select', [
                'label' => __('admin.locations.form.label.language'),
                'options' => \App\Models\Language::whereNotNull('active')->get()->pluck('name', 'id')->all(),
                'constraints' => 'required',
            ])
            ->add('featured', 'toggle', ['label' => __('admin.locations.form.label.featured')])
            ->add('csv', 'textarea', ['label' => __('admin.locations.form.label.csv'), 'placeholder' => 'e.g.:
USA;California;Anaheim
USA;California;Los Angeles;Hollywood
USA;California;Los Angeles;Angelino Heights
USA;California;San Bernardino
', 'constraints' => 'required'])
            ->add('mappicker', 'mappicker', ['label' => __('admin.locations.form.label.mappicker')])
            ->add('latitude', 'number', ['label' => __('admin.locations.form.label.latitude'), 'value' => config()->map->latitude, 'constraints' => 'required|min:-90|max:90'])
            ->add('longitude', 'number', ['label' => __('admin.locations.form.label.longitude'), 'value' => config()->map->longitude, 'constraints' => 'required|min:-180|max:180'])
            ->add('zoom', 'number', ['label' => __('admin.locations.form.label.zoom'), 'value' => config()->map->zoom, 'constraints' => 'required|min:0|max:20'])
            ->add('submit', 'submit', ['label' => __('admin.locations.form.label.submit')])
            ->handleRequest();

        if ($form->isSubmitted()) {           
            $input = $form->getValues();

            $language = \App\Models\Language::find($input->get('language_id'));

            if ($form->isValid()) {
                $strings = explode('<br />', \nl2br($input->csv));

                foreach ($strings as $string) {
                    $root = (new \App\Models\Location)->getRoot();

                    $current = $root;

                    $current->slug = '';

                    if ('' == trim($string)) {
                        continue;
                    }

                    $locations = explode(';', d($string));

                    foreach ($locations as $location) {
                        $location = trim(e($location));

                        if ('' == $location) {
                            continue;
                        }

                        $latitude = $input->latitude;
                        $longitude = $input->longitude;
                        $zoom = $input->zoom;

                        if (false !== strstr($location, '|')) {
                            $fragments = explode('|', $location);

                            if (4 == count($fragments)) {
                                list($location, $latitude, $longitude, $zoom) = explode('|', $location);
                            }
                        }

                        $temp = \App\Models\Location::where('_parent_id', $current->id)
                            ->where('name', 'like', '%"' . $language->locale . '":"' . $location . '"%')
                            ->first();

                        if (null === $temp) {
                            $temp = new \App\Models\Location();
                            $temp->appendTo($current);
                            $temp->fill([
                                'active' => 1,
                                'featured' => $input->featured,
                                'slug' => trim(slugify(d($location)) . '-' . $current->slug, '-'),
                                'logo_id' => bin2hex(random_bytes(16)),
                                'header_id' => bin2hex(random_bytes(16)),
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'zoom' => $zoom,
                            ]);

                            $temp->setTranslation('name', $location, config()->app->locale_fallback);

                            if (config()->app->locale_fallback != $language->locale) {
                                $temp->setTranslation('name', $location, $language->locale);
                            }

                            $temp->save();
                        }

                        $current = $temp;
                    }
                }
                
                return redirect(adminRoute('locations'))
                    ->with('success', view('flash/success', ['message' => __('admin.locations.alert.create_multiple.success')]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/locations/create-multiple', [
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    private function getForm($model)
    {
        return form($model)
//            ->add('active', 'toggle', ['label' => __('admin.locations.form.label.published'), 'value' => '1'])
            ->add('featured', 'toggle', ['label' => __('admin.locations.form.label.featured')])
            ->add('name', 'translatable', ['label' => __('admin.locations.form.label.name'), 'constraints' => 'transrequired'])
            ->add('slug', 'text', ['label' => __('admin.locations.form.label.slug'), 'sluggable' => 'name', 'constraints' => 'required|alphanumericdash|maxlength:120|unique:locations,slug' . (null !== $model->get($model->getPrimaryKey()) ? ',' . $model->get($model->getPrimaryKey()) : '')])
            ->add('short_description', 'translatable', ['label' => __('admin.locations.form.label.summary')])
            ->add('description', 'textarea', ['label' => __('admin.locations.form.label.description')])
            ->add('placement', 'radio', ['label' => __('admin.locations.form.label.placement'), 'options' => ['root' => __('admin.locations.form.label.new_location'), 'append' => __('admin.locations.form.label.sublocation_of'), 'before' => __('admin.locations.form.label.before'), 'after' => __('admin.locations.form.label.after')], 'constraints' => 'required'])
            ->add('location', 'cascading', ['cascading_source' => 'location'])
            ->add('mappicker', 'mappicker', ['label' => __('admin.locations.form.label.mappicker')])
            ->add('latitude', 'number', ['label' => __('admin.locations.form.label.latitude'), 'value' => config()->map->latitude, 'constraints' => 'required|min:-90|max:90'])
            ->add('longitude', 'number', ['label' => __('admin.locations.form.label.longitude'), 'value' => config()->map->longitude, 'constraints' => 'required|min:-180|max:180'])
            ->add('zoom', 'number', ['label' => __('admin.locations.form.label.zoom'), 'value' => config()->map->zoom, 'constraints' => 'required|min:0|max:20'])
            ->add('logo_id', 'dropzone', ['label' => __('admin.locations.form.label.logo'), 'upload_id' => '4'])
            ->add('header_id', 'dropzone', ['label' => __('admin.locations.form.label.header'), 'upload_id' => '5'])
            ->add('meta_title', 'translatable', ['label' => __('admin.locations.form.label.meta_title')])
            ->add('meta_keywords', 'translatable', ['label' => __('admin.locations.form.label.meta_keywords')])
            ->add('meta_description', 'translatable', ['label' => __('admin.locations.form.label.meta_description')]);
    }

}
