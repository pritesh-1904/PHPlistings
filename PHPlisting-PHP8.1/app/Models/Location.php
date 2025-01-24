<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Location
    extends \App\Src\Orm\Model
{
    use \App\Src\Orm\Traits\NestedSetTrait;

    protected $table = 'locations';
    protected $pending;
    protected $fillable = [
        'active',
        'featured',
        'name',
        'slug',
        'short_description',
        'description',
        'logo_id',
        'header_id',
        'latitude',
        'longitude',
        'zoom',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];
    protected $searchable = [
        'parent_id' => ['_parent_id', 'eq'],
        'location_id' => ['id', 'eq'],
    ];
    protected $translatable = [
        'name',
        'short_description',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];

    public function listings()
    {
        return $this->hasMany('App\Models\Listing');
    }

    public function users()
    {
        return $this->hasMany('App\Models\User');
    }

    public function taxes()
    {
        return $this->hasMany('App\Models\Tax');
    }

    public function logo()
    {
        return $this->hasOne('App\Models\File', 'document_id', 'logo_id');
    }

    public function header()
    {
        return $this->hasOne('App\Models\File', 'document_id', 'header_id');
    }

    public function getTree()
    {
        return $this->buildTree(
            $this->getQuery()
                ->orderBy('_left')
                ->get()
                ->each(function ($item, $key) {
                    return collect([
                        'id' => $item->id,
                        'name' => $item->name,
                        '_parent_id' => $item->get('_parent_id'),
                    ]);
                })
                ->orderBy('name', 'asc', locale()->getLocale())
                ->all(),
            1
        );    
    }

    private function buildTree(array $locations, $id)
    {
        $tree = [];

        foreach ($locations as $location) {
            if ($location->_parent_id == $id) {
                $node = ['key' => $location->id, 'title' => $location->name];
                if (count($children = $this->buildTree($locations, $location->id)) > 0) {
                    $node['children'] = $children;
                }
                $tree[] = $node;
            }
        }

        return $tree;
    }

    public function getDropdownTree($levels = null)
    {
        $tree = [];

        foreach ($this->getTree() as $location) {
            $tree[$location['key']] = $location['title'];
            $this->buildChildren($location, $location['title'], $levels, $tree);
        }

        return $tree;
    }

    private function buildChildren($location, $path, $levels, &$tree)
    {
        $depth = 0;

        if (isset($location['children'])) {
            $depth++;

            if (null === $levels || $depth < $levels) {
                foreach ($location['children'] as $child) {
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

        if ('' != $this->get('logo_id', '') && null !== $this->logo) {
            $this->logo->delete();
        }

        if ('' != $this->get('header_id', '') && null !== $this->header) {
            $this->header->delete();
        }

        db()->table('stats')
            ->where('type', 'location_impression')
            ->where('type_id', $this->id)
            ->delete();

        db()->table('rawstats')
            ->where('type', 'location_impression')
            ->where('type_id', $this->id)
            ->delete();

        $this->newQuery()
            ->where(function ($query) {
                $query
                    ->where('_right', '>=', $this->_right + 1)
                    ->orWhere('_left', '>=', $this->_right + 1);
            })
            ->update(db()->raw('_left = CASE WHEN _left >= ? THEN _left - ? ELSE _left END, _right = _right - ?', [$this->_right + 1, $this->getHeight(), $this->getHeight()]));

        return parent::delete($id);
    }

}
