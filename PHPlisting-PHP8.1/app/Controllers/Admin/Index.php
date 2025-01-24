<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Index
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        layout()->setTitle(__('admin.index.title.dashboard'));

        return response(layout()->content(
            view('admin/index/dashboard', [
                'types' => \App\Models\Type::whereNull('deleted')->orderBy('weight')->get(),
                'listings' => db()
                    ->table('listings')
                    ->select(db()->raw('COUNT(*) AS total, type_id'))
                    ->groupBy('type_id')
                    ->get([1]),
                'listingsapprove' => db()
                    ->table('listings')
                    ->select(db()->raw('COUNT(*) AS total, type_id'))
                    ->whereNull('active')
                    ->groupBy('type_id')
                    ->get([1]),
                'revenue' => db()
                    ->table('invoices')
                    ->select(db()->raw('SUM(total) AS revenue'))
                    ->where('status', 'paid')
                    ->first(['1'])
                    ->get('revenue'),
                'users' => db()
                    ->table('users')
                    ->count(),
                'usersapprove' => db()
                    ->table('users')
                    ->whereNull('active')
                    ->count(),
                'locations' => db()
                    ->table('locations')
                    ->whereNotNull('_parent_id')
                    ->count(),
            ])
        ));
    }

}
