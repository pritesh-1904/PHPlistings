<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class UploadType
    extends \App\Src\Orm\Model
{

    protected $table = 'uploadtypes';
    protected $fillable = [
        'name',
        'max_size',
        'max_files',
        'small_image_resize_type',
        'small_image_width',
        'small_image_height',
        'small_image_quality',
        'medium_image_resize_type',
        'medium_image_width',
        'medium_image_height',
        'medium_image_quality',
        'large_image_resize_type',
        'large_image_width',
        'large_image_height',
        'large_image_quality',
        'watermark_file_path',
        'watermark_position_vertical',
        'watermark_position_horizontal',
        'watermark_transparency',
        'cropbox_width',
        'cropbox_height',
    ];
    protected $translatable = [
        'name',
    ];
    protected $sortable = [
        'id' => ['id'],
        'name' => ['name'],
    ];

    public function fields()
    {
        return $this->hasMany('App\Models\Field', 'upload_id');
    }

    public function listingFields()
    {
        return $this->hasMany('App\Models\ListingField', 'upload_id');
    }

    public function files()
    {
        return $this->hasMany('App\Models\File');
    }

    public function fileTypes()
    {
        return $this->belongsToMany('App\Models\FileType');
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
        $this->fileTypes()->detach();

        foreach ($this->files as $file) {
            $file->delete();
        }
        
        return parent::delete($id);
    }

}
