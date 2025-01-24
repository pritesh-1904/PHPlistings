<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class FieldOption
    extends \App\Src\Orm\Model
{
    use \App\Src\Orm\Traits\SortableTrait;

    protected $table = 'fieldoptions';
    protected $fillable = [
        'name',
        'value',
    ];
    protected $searchable = [
        'field_id' => ['field_id', 'eq'],
    ];
    protected $sortable = [
        'weight',
    ];
    protected $translatable = [
        'value',
    ];

    public function field()
    {
        return $this->belongsTo('App\Models\Field');
    }

}
