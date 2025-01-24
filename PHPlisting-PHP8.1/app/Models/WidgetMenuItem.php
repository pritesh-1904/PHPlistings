<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class WidgetMenuItem
    extends \App\Src\Orm\Model
{
    use \App\Src\Orm\Traits\SortableTrait;

    protected $table = 'widgetmenuitems';
    protected $fillable = [
        'active',
        'public',
        'highlighted',
        'name',
        'route',
        'link',
        'target',
        'nofollow',
        '_parent_id',
    ];
    protected $searchable = [
        'parent_id' => ['_parent_id', 'eq'],
    ];
    protected $sortable = [
        'weight',
    ];
    protected $translatable = [
        'name',
    ];

    public function group()
    {
        return $this->belongsTo('\App\Models\WidgetMenuGroup');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'id', '_parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, '_parent_id');
    }

    public function getLink()
    {
        if ($this->page_id > 0) {
            return route($this->page->slug);
        } elseif (null !== $this->route && '' != $this->route) {
            return route($this->route);
        } elseif (null !== $this->link && '' != $this->link) {
            return $this->link;
        }

        return route('');
    }

}
