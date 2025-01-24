<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Tax
    extends \App\Src\Orm\Model
{
    use \App\Src\Orm\Traits\SortableTrait;

    protected $table = 'taxes';
    protected $fillable = [
        'location_id',
        'name',
        'value',
        'compound',
    ];
    protected $sortable = [
        'weight' => ['weight'],
    ];
    protected $translatable = [
        'name',
    ];

}
