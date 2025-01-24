<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Category
    extends \App\Src\Orm\Model
{
    use \App\Src\Orm\Traits\NestedSetTrait;

    protected $table = 'categories';
    protected $pending;
    protected $fillable = [
        'active',
        'featured',
        'name',
        'slug',
        'icon',
        'marker_color',
        'icon_color',
        'short_description',
        'description',
        'logo_id',
        'header_id',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];
    protected $searchable = [
        'parent_id' => ['_parent_id', 'eq'],
        'category_id' => ['id', 'eq'],
    ];
    protected $translatable = [
        'name',
        'short_description',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];

    public function isTypable()
    {
        return true;
    }

    public function listingType()
    {
        return $this->belongsTo('App\Models\Type');
    }

    public function listings()
    {
        return $this->hasMany('App\Models\Listing');
    }

    public function extraListings()
    {
        return $this->belongsToMany('App\Models\Listing');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product');
    }

    public function fields()
    {
        return $this->belongsToMany('App\Models\ListingField');
    }

    public function logo()
    {
        return $this->hasOne('App\Models\File', 'document_id', 'logo_id');
    }

    public function header()
    {
        return $this->hasOne('App\Models\File', 'document_id', 'header_id');
    }

    public function getTree($typeId, $pricingId = null, $expanded = false, $excludeId = null, $hideInactive = false, $hideEmpty = false)
    {
        $query = $this->newQuery()
            ->where('type_id', $typeId)
            ->orderBy('_left');

        if (false !== $hideInactive) {
            $query
                ->whereNotNull('active');
        }

        if (false !== $hideEmpty) {
            $query
                ->where('counter', '>', 0);
        }

        if (null !== $excludeId) {
            $query->where('id', '!=' , $excludeId);
        }

        if (null !== $pricingId && null !== $pricing = \App\Models\Pricing::find($pricingId)) {
            $query = $pricing->product->categories()
                 ->select('DISTINCT c2.id AS id, c2.name AS name, c2._parent_id as _parent_id, c2._left AS _left')
                 ->innerJoin($this->getQuery()->getTable(), $this->getQuery()->getTable() . '._right BETWEEN c2._left AND c2._right', 'c2')
                 ->orderBy('c2._left');

            if (false !== $hideInactive) {
                 $query
                     ->whereNotNull('c2.active');
            }

            if (false !== $hideEmpty) {
                 $query
                     ->where('c2.counter', '>', 0);
            }

            if (null !== $excludeId) {
                $query->where($this->getPrefixedTable() . '.id', '!=' , $excludeId);
            }

            $categories = $query->get([1]);
        } else {
            $categories = $query->get(['id', 'name', '_parent_id']);
        }

        return $this->buildTree(
            $categories
                ->each(function ($item, $key) {
                    return collect([
                        'id' => $item->id,
                        'name' => $item->name,
                        '_parent_id' => $item->get('_parent_id'),
                    ]);
                })
                ->orderBy('name', 'asc', locale()->getLocale())
                ->all(),
            $this->getRoot($typeId)->id,
            $expanded
        );
    }

    public function getExpandedTree($typeId, $pricingId = null, $hideInactive = false, $hideEmpty = false) {
        return $this->getTree($typeId, $pricingId, true, null, $hideInactive, $hideEmpty);
    }


    public function getAllTypesTree($expanded = false, $hideInactive = false, $hideEmpty = false)
    {
        $tree = [];

        $types = \App\Models\Type::query()
            ->whereNull('deleted')
            ->orderBy('weight')
            ->get();
        
        foreach ($types as $type) {
            if (count($children = $this->getTree($type->id, null, $expanded, null, $hideInactive, $hideEmpty)) > 0) {
                $tree[] = [
                    'key' => $this->getRoot($type->id)->id,
                    'title' => $type->name_plural,
                    'folder' => true,
                    'checkbox' => false,
                    'expanded' => $expanded,
                    'children' => $children,
                ];
            }
        }

        return $tree;
    }

    private function buildTree(array $categories, $id, $expanded = false)
    {
        $tree = [];

        foreach ($categories as $key => $category) {
            if ($category->_parent_id == $id) {
                $node = ['key' => $category->id, 'title' => $category->name];

                if (count($children = $this->buildTree($categories, $category->id, $expanded)) > 0) {
                    $node['expanded'] = $expanded;
                    $node['children'] = $children;
                } else {
                    unset($categories[$key]);
                }

                $tree[] = $node;
            }
        }

        return $tree;
    }

    public function getDropdownTree($typeId, $pricing_id = null, $levels = null, $hideInactive = false, $hideEmpty = false)
    {
        $tree = [];

        foreach ($this->getTree($typeId, $pricing_id, false, null, $hideInactive, $hideEmpty) as $category) {
            $tree[$category['key']] = $category['title'];
            $this->buildChildren($category, $category['title'], $levels, $tree);
        }

        return $tree;
    }

    private function buildChildren($category, $path, $levels, &$tree)
    {
        $depth = 0;
        
        if (isset($category['children'])) {
            $depth++;

            if (null === $levels || $depth < $levels) {
                foreach ($category['children'] as $child) {
                    $tree[$child['key']] = $path  . ' &raquo; ' . $child['title'];
                    $this->buildChildren($child, $path  . ' &raquo; ' . $child['title'], $levels, $tree);
                }
            }
        }
    }

    public function delete($id = null)
    {
        foreach ($this->descendants()->get() as $descendant) {
            $descendant->delete();
        }

        $this->fields()->detach();
        $this->products()->detach();        
        $this->extraListings()->detach();

        if ('' != $this->get('logo_id', '') && null !== $this->logo) {
            $this->logo->delete();
        }

        if ('' != $this->get('header_id', '') && null !== $this->header) {
            $this->header->delete();
        }

        db()->table('stats')
            ->where('type', 'category_impression')
            ->where('type_id', $this->id)
            ->delete();

        db()->table('rawstats')
            ->where('type', 'category_impression')
            ->where('type_id', $this->id)
            ->delete();

        $this->newQuery()
            ->where('type_id', $this->type_id)
            ->where(function ($query) {
                $query
                    ->where('_right', '>=', $this->_right + 1)
                    ->orWhere('_left', '>=', $this->_right + 1);
            })
            ->update(db()->raw('_left = CASE WHEN _left >= ? THEN _left - ? ELSE _left END, _right = _right - ?', [$this->_right + 1, $this->getHeight(), $this->getHeight()]));

        return parent::delete($id);
    }

}
