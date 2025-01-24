<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Rating
    extends \App\Src\Orm\Model
{
    use \App\Src\Orm\Traits\SortableTrait;

    protected $table = 'ratings';
    protected $fillable = [
        'name',
    ];
    protected $translatable = [
        'name',
    ];

    public function types()
    {
        return $this->belongsToMany('App\Models\Type');
    }

    public function getTree($exclude = null)
    {
        $tree = [];

        foreach ($this->newQuery()->orderBy('weight')->get() as $rating) {
            $tree[] = ['key' => $rating->id, 'title' => $rating->name];
        }

        return $tree;
    }

    public function performInsert()
    {
        $this->weight = (int) $this->getQuery()->max('weight') + 1;

        $id = parent::performInsert();

        return db()->table('reviews')->addColumn('rating_' . $id, 'float(3,1) UNSIGNED DEFAULT NULL');
    }

    public function delete($id = null)
    {
        $this->types()->detach();        

        db()->table('reviews')->dropColumn('rating_' . $this->id);

        $this->unsort();

        return parent::delete($id);
    }

}
