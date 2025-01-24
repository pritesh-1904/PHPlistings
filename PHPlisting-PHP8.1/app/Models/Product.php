<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Product
    extends \App\Src\Orm\Model
{
    use \App\Src\Orm\Traits\SortableTrait;

    protected $table = 'products';
    protected $fillable = [
        'hidden',
        'featured',
        'name',
        'description',
        '_featured',
        '_page',
        '_position',
        '_extra_categories',
        '_title_size',
        '_short_description_size',
        '_description_size',
        '_description_links_limit',
        '_gallery_size',
        '_address',
        '_map',
        '_event_dates',
        '_send_message',
        '_reviews',
        '_seo',
        '_backlink',
        '_dofollow',
    ];
    protected $sortable = [
        'weight' => ['weight'],
    ];
    protected $translatable = [
        'name',
        'description',
    ];

    public function listingType()
    {
        return $this->belongsTo('App\Models\Type');
    }

    public function pricings()
    {
        return $this->hasMany('App\Models\Pricing');
    }

    public function badges()
    {
        return $this->belongsToMany('App\Models\Badge');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category');
    }

    public function fields()
    {
        return $this->belongsToMany('App\Models\ListingField');
    }

    public function getTree($typeId = null, $categoryId = null, $pricingId = null, $withHidden = false, $withPricing = false, $withHiddenPricing = false, $withEmptyProducts = false)
    {
        $tree = [];

        $query = $this->getQuery();
        
        if (null !== $typeId) {
            $query->where('type_id', $typeId);
        }

        if (!$withHidden) {
            $query->whereNull('hidden');
        }

        if (null !== $categoryId) {
            $query->whereHas('categories', function ($relation) use ($categoryId) {
                $relation->where('category_id', $categoryId);
            });
        }

        if (null !== $pricingId) {
            if (null !== $pricing = \App\Models\Pricing::find($pricingId)) {
                if ($pricing->upgrades->count() > 0) {
                    $query
                        ->whereHas('pricings', function ($relation) use ($pricing, $withHiddenPricing) {
                            $relation->whereIn('id', $pricing->upgrades->pluck('id')->all());
                            if (!$withHiddenPricing) {
                                $relation->whereNull('hidden');
                            }
                        })
                        ->with('pricings', function ($relation) use ($pricing, $withHiddenPricing) {
                            $relation->whereIn('id', $pricing->upgrades->pluck('id')->all());
                            if (!$withHiddenPricing) {
                                $relation->whereNull('hidden');
                            }

                            $relation->orderBy('weight');
                        });
                } else {
                    return $tree;
                }
            }
        } else {
            $query->with('pricings', function ($relation) use ($withHiddenPricing) {
                if (!$withHiddenPricing) {
                    $relation->whereNull('hidden');
                }

                $relation->orderBy('weight');
            });
        }

        $products = $query->orderBy('weight')->get();

        $types = \App\Models\Type::whereNull('deleted')->orderBy('weight');

        if (null !== $typeId) {
            $types->where('id', $typeId);
        }

        foreach ($types->get() as $type) {
            $typeNode = [
                'key' => 't' . $type->id,
                'title' => $type->name_plural,
                'expanded' => true,
                'extraClasses' => 'tree-highlight',
            ];

            $productNodes = [];

            foreach ($products as $product) {
                if ($product->type_id != $type->id) {
                    continue;
                }

                $productNode = [
                    'key' => ($withPricing ? 'p' : '') . $product->id,
                    'title' => $product->name,
                ];

                $pricingNodes = [];

                if ($withPricing) {
                    foreach ($product->pricings as $pricing) {
                        $pricingNodes[] = ['key' => $pricing->id, 'title' => $pricing->getName()];
                    }

                    $productNode['expanded'] = true;
                    $productNode['children'] = $pricingNodes;
                }

                if (count($pricingNodes) > 0 || false === $withPricing || false !== $withEmptyProducts) {
                    $productNodes[] = $productNode;
                }

                if (count($productNodes) > 0 || false !== $withEmptyProducts) {
                    $typeNode['children'] = $productNodes;
                }
            }

            if (count($productNodes) > 0) {
                $tree[] = $typeNode;
            }
        }

        return $tree;
    }

    public function getTreeWithHidden($typeId = null, $categoryId = null, $pricingId = null)
    {
        return $this->getTree($typeId, $categoryId, $pricingId, true);
    }

    public function getTreeWithPricing($typeId = null, $categoryId = null, $pricingId = null)
    {
        return $this->getTree($typeId, $categoryId, $pricingId, false, true);
    }

    public function getTreeWithHiddenPricing($typeId = null, $categoryId = null, $pricingId = null)
    {
        return $this->getTree($typeId, $categoryId, $pricingId, true, true, true);
    }

    public function getDropdownTreeWithPricing($typeId = null, $categoryId = null, $pricingId = null, $withHidden = false, $withHiddenPricing = false)
    {
        $tree = [];

        foreach($this->getTree($typeId, $categoryId, $pricingId, $withHidden, true, $withHiddenPricing) as $product) {
            $this->buildChildren($product, $product['title'], $tree);
        }

        return $tree;
    }

    public function getDropdownTreeWithHiddenPricing($typeId = null, $categoryId = null, $pricingId = null)
    {
        return $this->getDropdownTreeWithPricing($typeId, $categoryId, $pricingId, true, true);
    }

    private function buildChildren($product, $path, &$tree)
    {
        if (isset($product['children'])) {
            foreach($product['children'] as $child) {
                $tree[$child['key']] = $path  . ' &raquo; ' . $child['title'];
                $this->buildChildren($child, $path  . ' &raquo; ' . $child['title'], $tree);
            }
        }
    }

    public function delete($id = null)
    {
        foreach ($this->pricings as $pricing) {
            $pricing->delete();
        }

        $this->badges()->detach();
        $this->categories()->detach();
        $this->fields()->detach();
        
        $this->unsort();

        return parent::delete($id);
    }

}
