<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class FileType
    extends \App\Src\Orm\Model
{

    protected $table = 'filetypes';
    protected $fillable = [
        'name',
        'mime',
        'extension',
    ];
    protected $translatable = [
        'name',
    ];

    public function uploadTypes()
    {
        return $this->belongsToMany('App\Models\UploadType');
    }

    public function getTree()
    {
        $tree = [];

        foreach($this->getQuery()->get(['id', 'name']) as $type) {
            $tree[] = [
                'key' => $type->id,
                'title' => $type->name,
            ];
        }

        return $tree;
    }

    public function delete($id = null)
    {
        $this->uploadTypes()->detach();
        
        return parent::delete($id);
    }

}
