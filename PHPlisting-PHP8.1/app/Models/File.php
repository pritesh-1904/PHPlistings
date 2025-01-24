<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class File
    extends \App\Src\Orm\Model
{

    protected $table = 'files';
    protected $fillable = [
        'name',
        'extension',
        'document_id',
        'uploadtype_id',
        'version',
        'mime',
        'size',
        'user_id',
        'ip',
        'title',
        'description',
    ];
    protected $searchable = [
        'name' => ['name', 'like'],
        'uploadtype_id' => ['uploadtype_id', 'eq'],
    ];
    protected $sortable = [
        'id' => ['id'],
        'name' => ['name'],
        'size' => ['size'],
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\UploadType');
    }

    public function delete($id = null)
    {
        foreach (['small', 'medium', 'large'] as $type) {
            if (null !== $this->$type() && file_exists($this->$type()->getPath())) {
                unlink($this->$type()->getPath());
            }
        }

        unlink($this->getPath());

        return parent::delete($id);
    }

    public function getUrl()
    {
        return route('media/' . $this->id . '/' . (isset($this->_type) ? $this->_type . '/' : '') . $this->name . '.' . $this->extension, ['v' => $this->version]);
    }

    public function getPath()
    {
        return config()->app->storage_path . DS . (isset($this->_type) ? ucfirst($this->_type) . DS : '') . $this->id;
    }

    public function isImage()
    {
        return in_array($this->mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    public function getWidth()
    {
        return $this->get((isset($this->_type) ? $this->_type . '_image_width' : 'image_width'));
    }

    public function getHeight()
    {
        return $this->get((isset($this->_type) ? $this->_type . '_image_height' : 'image_height'));
    }

    public function __call($method, $parameters)
    {
        if (in_array($method, [
            'small',
            'medium',
            'large',
        ])) {
            $model = clone $this;
            $model->_type = $method;

            if (null !== $this->get('_legacy')) {
                $model->extension = 'jpg';
            }

            if (!$this->isImage()) {
                return null;
            }
            
            return $model;
        }

        return parent::__call($method, $parameters);
    }

}
