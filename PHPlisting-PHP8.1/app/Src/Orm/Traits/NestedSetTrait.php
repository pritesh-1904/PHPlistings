<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Orm\Traits;

trait NestedSetTrait {

    public function isTypable()
    {
        return false;
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'id', '_parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, '_parent_id');
    }

    public function ancestors()
    {
        $query = $this->newQuery()
            ->where('_left', '<', $this->_left)
            ->where('_right', '>', $this->_right)
            ->orderBy('_left');

        if ($this->isTypable()) {
            $query->where('type_id', $this->type_id);
        }

        return $query;
    }

    public function ancestorsAndSelf()
    {
        $query = $this->newQuery()
            ->where('_left', '<=', $this->_left)
            ->where('_right', '>=', $this->_right)
            ->orderBy('_left');

        if ($this->isTypable()) {
            $query->where('type_id', $this->type_id);
        }

        return $query;
    }

    public function ancestorsWithoutRoot()
    {
        return $this
            ->ancestors()
            ->whereNotNull('_parent_id');
    }

    public function ancestorsAndSelfWithoutRoot()
    {
        return $this
            ->ancestorsAndSelf()
            ->whereNotNull('_parent_id');
    }

    public function descendants()
    {
        $query = $this->newQuery()
            ->whereBetween('_left', [$this->_left + 1, $this->_right])
            ->orderBy('_left');

        if ($this->isTypable()) {
            $query->where('type_id', $this->type_id);
        }

        return $query;
    }

    public function descendantsAndSelf()
    {
        $query = $this->newQuery()
            ->whereBetween('_left', [$this->_left, $this->_right])
            ->orderBy('_left');

        if ($this->isTypable()) {
            $query->where('type_id', $this->type_id);
        }

        return $query;    
    }

    public function siblings()
    {
        $query = $this->newQuery()
            ->where($this->getPrimaryKey(), '!=', $this->get($this->getPrimaryKey()))
            ->where('_parent_id', $this->_parent_id)
            ->orderBy('_left');

        if ($this->isTypable()) {
            $query
                ->where('type_id', $this->type_id);
        }

        return $query;    
    }

    public function siblingsAndSelf()
    {
        $query = $this->newQuery()
            ->where('type_id', $this->type_id)
            ->where('_parent_id', $this->_parent_id)
            ->orderBy('_left');

        if ($this->isTypable()) {
            $query
                ->where('type_id', $this->type_id);
        }

        return $query;
    }

    public function getRoot($typeId = null)
    {
        $query = $this->newQuery()
            ->whereNull('_parent_id');

        if ($this->isTypable()) {
            if (null == $typeId) {
                throw new \Exception('Type ID is required');
            }
            
            $query
                ->where('type_id', $typeId);
        }

        return $query->first();    
    }

    public function getLevel()
    {
        return $this
            ->ancestors()
            ->count();
    }

    public function getHeight()
    {
        if (!$this->exists) return 2;

        return $this->_right - $this->_left + 1;
    }

    public function isRoot()
    {
        return (null === $this->_parent_id);
    }

    public function isLeaf()
    {
        return $this->_left + 1 == $this->_right;
    }

    public function isChildOf(self $node)
    {
        return $this->_parent_id == $node->get($node->getPrimaryKey());
    }

    public function setRoot()
    {
        $this->_parent_id = null;
        $this->pending = ['insertRoot'];

        return $this;
    }

    public function appendTo(self $node)
    {
        $this->_parent_id = $node->id;
        $this->pending = ['appendOrPrependTo', $node];

        return $this;
    }

    public function prependTo(self $node)
    {
        $this->_parent_id = $node->_parent_id;
        $this->pending = ['appendOrPrependTo', $node, true];

        return $this;
    }

    public function insertBefore(self $node)
    {
        $this->_parent_id = $node->_parent_id;
        $this->pending = ['insertBeforeOrAfter', $node];

        return $this;
    }

    public function insertAfter(self $node)
    {
        $this->_parent_id = $node->_parent_id;
        $this->pending = ['insertBeforeOrAfter', $node, true];

        return $this;
    }

    private function insertBeforeOrAfter(self $node, $after = false)
    {
        return $this->insertAt($after ? $node->_right + 1 : $node->_left);
    }

    private function appendOrPrependTo(self $node, $prepend = false)
    {
        return $this->insertAt($prepend ? $node->_left + 1 : $node->_right);
    }

    private function insertRoot()
    {
        $query = $this->newQuery();
        
        if ($this->isTypable()) {
            $query
                ->where('type_id', $this->type_id);
        }
        
        $max = $query->max('_right');

        if (!$this->exists) {
            $this->_left = $max + 1;
            $this->_right = $max + 2;
        } else {
            return $this->insertAt($max + 1);
        }
    }

    private function insertAt($position)
    {       
        if (!$this->exists) {
            return $this->insertNode($position);
        } else {
            return $this->moveNode($position);
        }
    }

    private function insertNode($position)
    {
        $query = $this->newQuery();

        if ($this->isTypable()) {
            $query
                ->where('type_id', $this->type_id);
        }

        $query
            ->where(function ($query) use ($position) {
                $query
                    ->where('_right', '>=', $position)
                    ->orWhere('_left', '>=', $position);
            })
            ->update(
                db()->raw(
                    '_left = CASE WHEN _left >= ? THEN _left + ? ELSE _left END, ' .
                    '_right = CASE WHEN _right >= ? THEN _right + ? ELSE _right END',
                    [$position, $this->getHeight(), $position, $this->getHeight()]
                )
            );

        $this->_left = $position;
        $this->_right = $position + $this->getHeight() - 1;

        return true;
    }

    private function moveNode($position)
    {
        $start = min($this->_left, $position);
        $stop = max($this->_right, $position - 1);
        $height = $this->getHeight();
        $distance = $stop - $start + 1 - $height;

        if ($this->_left < $position && $this->_right >= $position) {
            return false;
        }

        if ($position > $this->_left) {
            $height *= -1;
        } else {
            $distance *= -1;
        }
        
        $query = $this->newQuery();

        if ($this->isTypable()) {
            $query
                ->where('type_id', $this->type_id);
        }

        $query
            ->where(function($query) use ($start, $stop) {
                $query
                    ->whereBetween('_left', [$start, $stop])
                    ->orWhereBetween('_right', [$start, $stop]);
                }
            )
            ->update(
                db()->raw(
                    '_left = ' .
                    'CASE WHEN _left BETWEEN ? AND ? THEN _left + ? ' . 
                    'WHEN _left BETWEEN ? AND ? THEN _left + ? '.
                    'ELSE _left END, ' .
                    '_right = ' .
                    'CASE WHEN _right BETWEEN ? AND ? THEN _right + ? ' . 
                    'WHEN _right BETWEEN ? AND ? THEN _right + ? '.
                    'ELSE _right END',
                    [
                        $this->_left, $this->_right, $distance, $start, $stop, $height,
                        $this->_left, $this->_right, $distance, $start, $stop, $height
                    ]
                )
            );

            return true;
    }

    public function save($id = null) {
        if (null !== $this->pending) {
            $method = array_shift($this->pending);
            if (false === call_user_func_array([$this, $method], $this->pending)) {
                return false;
            }
        }

        $this->pending = null;

        return parent::save($id);
    }

    public function delete($id = null)
    {
        $this->descendants()->delete()->execute();

        $query = $this->newQuery();

        if ($this->isTypable()) {
            $query
                ->where('type_id', $this->type_id);
        }

        $query
            ->where('_right', '>=', $this->_right + 1)
            ->orWhere('_left', '>=', $this->_right + 1)
            ->update(
                db()->raw(
                    '_left = CASE WHEN _left >= ? THEN _left - ? ELSE _left END, _right = _right - ?',
                    [$this->_right + 1, $this->getHeight(), $this->getHeight()]
                )
            );

        return parent::delete($id);
    }
    
}
