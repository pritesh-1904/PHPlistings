<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Badge
    extends \App\Src\Orm\Model
{
    use \App\Src\Orm\Traits\SortableTrait;

    protected $table = 'badges';
    protected $fillable = [
        'active',
        'name',
        'image_id',
    ];
    protected $sortable = [
        'weight',
    ];
    protected $translatable = [
        'name',
    ];

    public function types()
    {
        return $this->belongsToMany('App\Models\Type');
    }

    public function listings()
    {
        return $this->belongsToMany('App\Models\Listing');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product');
    }

    public function image()
    {
        return $this->hasOne('App\Models\File', 'document_id', 'image_id');
    }

    public function delete($id = null)
    {
        $this->listings()->detach();
        $this->products()->detach();

        if ('' != $this->get('image_id', '') && null !== $this->image) {
            $this->image->delete();
        }

        $this->unsort();

        return parent::delete($id);
    }

}
