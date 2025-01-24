<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Field
    extends \App\Src\Orm\Model
{
    use \App\Src\Orm\Traits\SortableTrait;

    protected $table = 'fields';
    protected $fillable = [
        'submittable',
        'updatable',
        'outputable',
        'type',
        'upload_id',
        'label',
        'name',
        'value',
        'placeholder',
        'description',
    ];
    protected $sortable = [
        'weight'
    ];
    protected $translatable = [
        'label',
        'placeholder',
        'description',
    ];

    public function group()
    {
        return $this->belongsTo('App\Models\FieldGroup');
    }

    public function options()
    {
        return $this->hasMany('App\Models\FieldOption');
    }

    public function constraints()
    {
        return $this->hasMany('App\Models\FieldConstraint');
    }

    public function getTree()
    {
        $fields = $this->getQuery()
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
        $query = $this->getQuery();

        if (count($types) > 0) {
            $query->where(function ($query) use ($types) {
                foreach ($types as $type) {
                    $query->orWhere('type', $type);
                }
            });
        }

        return $query->get()->pluck('label', 'id')->all();
    }

    public function getLabel()
    {
        return $this->get('label');
    }

    public function getConstraints()
    {
        return $this->constraints->uasort(function($a, $b) {
            return $a->weight <=> $b->weight;
        })->pluck(function ($item) {
            return $item->name . ':' . $item->value;
        })->implode('|');
    }

    public function getOptions()
    {
        return $this->options
            ->uasort(function ($a, $b) {
                return $a->weight <=> $b->weight;
            })
            ->pluck('value', 'name')
            ->all();
    }

    public function getDefaultValue()
    {
        return $this->get('value');
    }

    public function getSluggable()
    {
        return $this->get('sluggable');
    }

    public function getPlaceholder()
    {
        return $this->get('placeholder');
    }

    public function getDescription()
    {
        return $this->get('description');
    }

    public function getItemProperty()
    {
        return null;
    }

    public function getIcon()
    {
        return null;
    }

    public function delete($id = null)
    {
        foreach ($this->constraints()->get() as $constraint) {
            $constraint->delete();
        }

        foreach ($this->options()->get() as $option) {
            $option->delete();
        }

        $this->unsort();

        return parent::delete($id);
    }

}
