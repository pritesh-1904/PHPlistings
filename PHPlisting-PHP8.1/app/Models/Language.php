<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Language
    extends \App\Src\Orm\Model
{
    use \App\Src\Orm\Traits\SortableTrait;

    protected $table = 'languages';
    protected $fillable = [
        'active',
        'locale',
        'name',
        'native',
        'direction',
        'thousands_separator',
        'decimal_separator',
        'decimal_places',
        'date_format',
        'time_format',
    ];
    protected $sortable = [
        'weight',
    ];

}
