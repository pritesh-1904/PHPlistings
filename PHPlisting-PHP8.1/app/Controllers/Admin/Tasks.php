<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Tasks
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.tasks.title.index'));

        if (null !== request()->post->get('action') && null !== request()->post->get('id')) {
            switch (request()->post->action) {
                case 'unlock':
                    db()->table('cronjobs')
                        ->whereIn('id', (array) request()->post->id)
                        ->whereNotNull('locked')
                        ->update(['locked' => null]);

                    $alert = view('flash/success', ['message' => __('admin.tasks.alert.unlock.success')]);

                    break;
            }
        }

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'asc';
        }

        $tasks = \App\Models\Cronjob::search()
            ->paginate();

        $table = dataTable($tasks)
            ->addColumns([
                'name' => [__('admin.tasks.datatable.label.name')],
                'status' => [__('admin.tasks.datatable.label.status'), function ($task) {
                    return view('misc/status', [
                        'type' => 'task',
                        'status' => $task->locked,
                    ]);
                }],
                'last_run_datetime' => [__('admin.tasks.datatable.label.last_run_datetime'), function ($task) {
                    return locale()->formatDatetimeDiff($task->last_run_datetime);
                }],
            ])
            ->orderColumns([
                'name',
            ])
            ->addBulkActions([
                'unlock' => __('admin.tasks.datatable.bulkaction.unlock'),
            ]);

        return response(layout()->content(
            view('admin/tasks/index', [
                'alert' => $alert ?? null,
                'tasks' => $table,
            ])
        ));
    }

}
