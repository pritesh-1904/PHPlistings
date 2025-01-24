<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Export
    extends \App\Src\Orm\Model
{

    protected $table = 'exports';
    protected $fillable = [
        'language_id',
        'categories',
        'pricings',
        'fields',
    ];
    protected $sortable = [
        'id' => ['id'],
        'added_datetime' => ['added_datetime'],
    ];

    public function type()
    {
        return $this->belongsTo('App\Models\Type');
    }

    public function language()
    {
        return $this->belongsTo('App\Models\Language');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category');
    }

    public function pricings()
    {
        return $this->belongsToMany('App\Models\Pricing');
    }

    public function fields()
    {
        return $this->belongsToMany('App\Models\ListingField');
    }

    public function delete($id = null)
    {
        if (file_exists($this->getPath())) {
            unlink($this->getPath());
        }

        $this->categories()->detach();
        $this->pricings()->detach();
        $this->fields()->detach();

        return parent::delete($id);
    }

    public function getPath()
    {
        return ROOT_PATH_PROTECTED . DS . 'Exports' . DS . $this->get('id');
    }

}
