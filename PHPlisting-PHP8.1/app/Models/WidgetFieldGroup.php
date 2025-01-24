<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class WidgetFieldGroup
    extends \App\Src\Orm\Model
{

    protected $table = 'widgetfieldgroups';
    protected $fillable = [
        'name',
        'slug',
    ];
    protected $sortable = [
        'id' => ['id'],
        'name' => ['name'],
    ];

    public function fields()
    {
        return $this->hasMany('App\Models\WidgetField');
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

    public function delete($id = null)
    {
        foreach ($this->fields()->get() as $field) {
            $field->delete();
        }

        return parent::delete($id);
    }
}
