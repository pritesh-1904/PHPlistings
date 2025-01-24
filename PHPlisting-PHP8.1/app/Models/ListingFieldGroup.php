<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class ListingFieldGroup
    extends \App\Src\Orm\Model
{

    protected $table = 'listingfieldgroups';
    protected $fillable = [
        'name',
        'slug',
    ];
    protected $translatable = [
        'name',
    ];
    protected $sortable = [
        'id' => ['id'],
        'name' => ['name'],
    ];

    public function fields()
    {
        return $this->hasMany('App\Models\ListingField');
    }

    public function getTree()
    {
        $fields = $this
            ->fields()
            ->orderBy('weight')
            ->get();

        $tree = [];

        foreach ($fields as $field) {
            $tree[] = ['key' => $field->id, 'title' => $field->label];
        }

        return $tree;
    }

    public function getDropdownTree(array $types = [])
    {
        $query = $this->fields();

        if (count($types) > 0) {
            $query->where(function ($query) use ($types) {
                foreach ($types as $type) {
                    $query->orWhere('type', $type);
                }
            });
        }

        return $query->get()->pluck('label', 'id')->all();
    }

}
