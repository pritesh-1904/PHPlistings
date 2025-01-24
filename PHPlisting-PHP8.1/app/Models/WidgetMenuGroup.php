<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class WidgetMenuGroup
    extends \App\Src\Orm\Model
{

    protected $table = 'widgetmenugroups';
    protected $fillable = [
        'name',
    ];
    protected $sortable = [
        'id' => ['id'],
        'name' => ['name'],
    ];

    public function menuItems()
    {
        return $this->hasMany('\App\Models\WidgetMenuItem');
    }

    public function getTree()
    {
        $query = $this->menuItems()
            ->where('active', 1)
            ->where('_parent_id', 0)
            ->orderBy('weight')
            ->with('children', function ($query) {
                $query
                    ->where('active', 1)
                    ->orderBy('weight');
                if (!auth()->check()) {
                    $query->whereNotNull('public');
                }
            });

        if (!auth()->check()) {
            $query->whereNotNull('public');
        }

        return $query->get();
    }

    public function getParentsDropdownTree()
    {
        return $this->menuItems()
            ->where('active', 1)
            ->where('_parent_id', 0)
            ->orderBy('weight')
            ->get()
            ->pluck('name', 'id')
            ->all();
    }

}
