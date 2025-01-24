<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Ratings
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.ratings.title.index'));

        $ratings = \App\Models\Rating::search(null, [], 'admin/rating-categories')
            ->orderBy('weight')
            ->paginate();

        $table = dataTable($ratings)
            ->addColumns([
                'id' => [__('admin.ratings.datatable.label.id')],
                'name' => [__('admin.ratings.datatable.label.name')],
            ])
            ->addActions([
                'edit' => [__('admin.ratings.datatable.action.edit'), function ($rating) {
                    return adminRoute('rating-categories/update/' . $rating->id);
                }],
                'delete' => [__('admin.ratings.datatable.action.delete'), function ($rating) {
                    return adminRoute('rating-categories/delete/' . $rating->id);
                }],
            ])
            ->setSortable('ratings');

        return response(layout()->content(
            view('admin/rating-categories/index', [
                'ratings' => $table,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (!auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.ratings.title.create'));

        $rating = new \App\Models\Rating();

        $form = form($rating)
            ->add('name', 'translatable', ['label' => __('admin.ratings.form.label.name'), 'constraints' => 'transrequired'])
            ->add('submit', 'submit', ['label' => __('admin.ratings.form.label.submit')])
            ->handleRequest();
        
        if ($form->isSubmitted()) {           

            if ($form->isValid()) {
                $rating->save();

                return redirect(adminRoute('rating-categories', session()->get('admin/rating-categories')))
                    ->with('success', view('flash/success', ['message' => __('admin.ratings.alert.create.success', ['name' => $rating->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/rating-categories/create', [
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

        if (null === $rating = \App\Models\Rating::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.ratings.title.update'));

        $form = form($rating)
            ->add('name', 'translatable', ['label' => __('admin.ratings.form.label.name'), 'constraints' => 'transrequired'])
            ->add('submit', 'submit', ['label' => __('admin.ratings.form.label.update')])
            ->handleRequest();

        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                $rating->save();

                return redirect(adminRoute('rating-categories', session()->get('admin/rating-categories')))
                    ->with('success', view('flash/success', ['message' => __('admin.ratings.alert.update.success', ['name' => $rating->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/rating-categories/update', [
                'rating' => $rating,
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

        if (null === $rating = \App\Models\Rating::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $rating->delete();

        return redirect(adminRoute('rating-categories', session()->get('admin/rating-categories')))
            ->with('success', view('flash/success', ['message' => __('admin.ratings.alert.remove.success', ['name' => $rating->name])]));
    }

}
