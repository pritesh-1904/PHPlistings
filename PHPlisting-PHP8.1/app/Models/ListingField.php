<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class ListingField
    extends \App\Src\Orm\Model
{
    use \App\Src\Orm\Traits\SortableTrait;

    protected $table = 'listingfields';
    protected $fillable = [
        'submittable',
        'updatable',
        'queryable',
        'outputable',
        'outputable_search',
        'sluggable',
        'type',
        'icon',
        'upload_id',
        'socialprofiletype_id',
        'search_type',
        'range_min',
        'range_max',
        'range_step',
        'label',
        'name',
        'value',
        'description',
        'placeholder',
        'schema_itemprop',
    ];
    protected $searchable = [
        'type_id' => ['type_id', 'eq'],
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
        return $this->belongsTo('App\Models\ListingFieldGroup');
    }

    public function listingType()
    {
        return $this->belongsTo('App\Models\Type');
    }

    public function socialProfileType()
    {
        return $this->belongsTo('App\Models\SocialProfileType');
    }

    public function options()
    {
        return $this->hasMany('App\Models\ListingFieldOption');
    }

    public function constraints()
    {
        return $this->hasMany('App\Models\ListingFieldConstraint');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product');
    }

    public function getTree($typeId, $groupId = null)
    {
        $query = $this->getQuery()
            ->where('type_id', $typeId);

        if (null !== $groupId) {
            $query->where('listingfieldgroup_id', $groupId);
        }

        $fields = $query
            ->orderBy('weight')
            ->get();

        $tree = [];

        if (null === $groupId) {
            $groups = \App\Models\ListingFieldGroup::orderBy('id')->get();

            foreach ($groups as $group) {
                $node = [
                    'key' => 'g' . $group->id,
                    'title' => $group->name,
                    'expanded' => true,
                    'extraClasses' => 'tree-highlight',
                ];

                $children = [];

                foreach ($fields as $field) {
                    if ($group->id == $field->listingfieldgroup_id) {
                        $children[] = ['key' => $field->id, 'title' => $field->label];
                    }
                }

                if (count($children) > 0) {
                    $node['children'] = $children;
                }

                $tree[] = $node;
            }
        } else {
            foreach ($fields as $field) {
                $tree[] = ['key' => $field->id, 'title' => $field->label];
            }
        }

        return $tree;
    }

    public function getDropdownTree($typeId, $groupId, array $types = [])
    {
        $query = $this->getQuery()
            ->where('type_id', $typeId)
            ->where('listingfieldgroup_id', $groupId);

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
        return $this->get('schema_itemprop');
    }

    public function getIcon()
    {
        return $this->get('icon');
    }

    public function delete($id = null)
    {
        $this->categories()->detach();
        $this->products()->detach();

        $this->constraints()->delete();
        $this->options()->delete();

        $this->unsort();

        return parent::delete($id);
    }

}
