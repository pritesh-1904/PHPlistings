<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class ListingFieldConstraint
    extends \App\Src\Orm\Model
{
    use \App\Src\Orm\Traits\SortableTrait;

    protected $table = 'listingfieldconstraints';
    protected $fillable = [
        'field_id',
        'name',
        'value',
    ];
    protected $searchable = [
        'field_id' => ['field_id', 'eq'],
    ];
    protected $sortable = [
        'weight',
    ];

    public function field()
    {
        return $this->belongsTo('App\Models\ListingField');
    }

}
