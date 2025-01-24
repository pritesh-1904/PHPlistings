<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Import
    extends \App\Src\Orm\Model
{

    protected $table = 'imports';
    protected $fillable = [
        'language_id',
        'pricing_id',
        'user_id',
        'active',
        'claimed',
        'notification',
    ];
    protected $sortable = [
        'id' => ['id'],
        'added_datetime' => ['added_datetime'],
    ];

    public function language()
    {
        return $this->belongsTo('App\Models\Language');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\Type');
    }

    public function pricing()
    {
        return $this->belongsTo('App\Models\Pricing');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function delete($id = null)
    {
        if (file_exists($this->getPath())) {
            unlink($this->getPath());
        }

        if (file_exists($this->getLogPath())) {
            unlink($this->getLogPath());
        }

        return parent::delete($id);
    }

    public function getPath()
    {
        return ROOT_PATH_PROTECTED . DS . 'Imports' . DS . $this->get('id');
    }

    public function getLogPath()
    {
        return ROOT_PATH_PROTECTED . DS . 'Imports' . DS . 'Logs' . DS . $this->get('id');
    }

}
