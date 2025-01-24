<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Orm\Traits;

trait SortableTrait {

    public function insertAfter(self $node)
    {
        $this->unsort();

        $node = $node->fresh();

        $this->weight = $node->weight + 1;

        return $this->newQuery()
            ->where('weight', '>', $node->weight)
            ->update(db()->raw('weight = weight + 1'));
    }    

    public function insertBefore(self $node)
    {
        $this->unsort();

        $node = $node->fresh();

        $this->weight = $node->weight;

        return $this->newQuery()
            ->where('weight', '>=', $node->weight)
            ->update(db()->raw('weight = weight + 1'));
    }    
    
    public function unsort()
    {
        if ($this->exists) {
            return $this->newQuery()
                ->where('weight', '>', $this->weight)
                ->update(db()->raw('weight = weight - 1'));
        }

        return true;
    }

    public function performInsert()
    {
        $this->weight = (int) $this->newQuery()->max('weight') + 1;
        
        return parent::performInsert();
    }

    public function delete($id = null)
    {
        $this->unsort();
        
        return parent::delete($id);
    }

}
