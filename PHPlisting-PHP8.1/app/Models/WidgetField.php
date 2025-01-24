<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class WidgetField
    extends \App\Src\Orm\Model
{
    use \App\Src\Orm\Traits\SortableTrait;

    protected $table = 'widgetfields';
    protected $fillable = [
        'type',
        'upload_id',
        'label',
        'name',
        'value',
        'description',
        'placeholder',
    ];
    protected $sortable = [
        'weight' => ['weight'],
    ];
    protected $translatable = [
        'label',
        'placeholder',
        'description',
    ];

    public function group()
    {
        return $this->belongsTo('App\Models\WidgetFieldGroup');
    }

    public function options()
    {
        return $this->hasMany('App\Models\WidgetFieldOption');
    }

    public function constraints()
    {
        return $this->hasMany('App\Models\WidgetFieldConstraint');
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
        return;
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

    public function performInsert()
    {
        $this->weight = (int) $this->getQuery()->max('weight') + 1;

        return parent::performInsert();
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
