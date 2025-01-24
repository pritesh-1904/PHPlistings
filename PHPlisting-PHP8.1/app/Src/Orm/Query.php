<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Orm;

class Query
    extends \App\Src\Dbal\QueryBuilder
{

    protected $model;
    public $with = [];

    public function __construct(\App\Src\Orm\Model $model)
    {
        $this->model = $model;
        $this->setConnection(db());
    }

    public function getQuery()
    {
        return $this->getModel()->getQuery();
    }

    public function getModel()
    {
        return $this->model;
    }

    public function with($name, \Closure $constraint = null)
    {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                if ($value instanceof \Closure) {
                    $this->with($key, $value);
                } else {
                    $this->with($value);
                }
            }
        } else {
            if (strstr($name, '.')) {
                $progress = [];
                $segments = explode('.', $name);

                foreach ($segments as $segment) {
                    $progress[] = $segment;
                    $this->with[implode('.', $progress)] = $constraint;
                }
            } else {        
                $this->with[$name] = $constraint;
            }
        }

        return $this;
    }

    public function has($name, $expression = '>', $value = 0, \Closure $constraint = null, $attribute = 'and')
    {
        $relation = \App\Src\Orm\Relations\Relation::noConstraints(function() use ($name) {
            return $this->getModel()->$name();
        });

        if (null !== $constraint) {
            $constraint($relation);
        }

        if ($value == 0 && in_array($expression, ['>', '!=', '<>'])) {
            $this->whereExists($relation->addWhereExistsConstraints()->getRelatedModelQuery(), $attribute);
        } else {
            $this->where($relation->addWhereHasConstraints()->getRelatedModelQuery(), $expression, $value, $attribute);
        }

        return $this;
    }

    public function whereHas($relation, \Closure $constraint = null)
    {
        return $this->has($relation, '>', 0, $constraint);
    }

    public function orWhereHas($relation, \Closure $constraint = null)
    {
        return $this->has($relation, '>', 0, $constraint, 'or');
    }

    public function whereHasNot($relation, \Closure $constraint = null)
    {
        return $this->has($relation, '=', 0, $constraint);
    }

    public function orWhereHasNot($relation, \Closure $constraint = null)
    {
        return $this->has($relation, '=', 0, $constraint, 'or');
    }

    //

    public function get(array $columns = null)
    {        
        if (null === $columns) {            
            $columns = [$this->getModel()->getPrefixedTable() . '.*'];
        }

        return $this->eagerLoadRelations($this->fetchObjects($columns, new Collection(), get_class($this->getModel()), [[], true]));
    }

    public function paginate($limit = null, array $columns = null)
    {       
        $page = 0;

        $total = $this->count();

        if (null === $limit) {
            $limit = 10;
        }

        if (null !== request()->get->get('limit') && is_numeric(request()->get->get('limit')) && request()->get->get('limit') > 0) {
            $limit = request()->get->get('limit');
        }

        if ($limit < 0) {
            $limit = 0;
        }

        if ($limit > 100) {
            $limit = 100;
        }

        if (null !== request()->get->get('page') && is_numeric(request()->get->get('page')) && request()->get->get('page') > 0) {
            $page = (int) request()->get->page - 1;
        }

        if ($total <= (int) $page * $limit) {
            $page = 0;
            $this->offset(0);
        } else {
            $this->offset($page * $limit);
        }

        request()->get->page = $page + 1;

        return $this
            ->limit($limit)
            ->get($columns)
            ->setTotal($total)
            ->setLimit($limit);
    }

    public function first(array $columns = null)
    {        
        if (null === $columns) {
            $columns = [$this->getModel()->getPrefixedTable() . '.*'];
        }

        $model = $this->fetchObject($columns, get_class($this->getModel()), [[], true]);

        if (null !== $model) {
            $this->eagerLoadRelations($model);
        }

        return $model;
    }

    protected function eagerLoadRelations($models)
    {
        foreach ($this->with as $name => $constraint) {
            if (!strstr($name, '.')) {
                $models = $this->eagerLoadRelation($models, $name, $constraint);
            }
        }

        return $models;
    }
    
    public function eagerLoadRelation($models, $name, $constraint = null)
    {
        if (false !== ($models instanceof \App\Src\Orm\Collection) && $models->count() == 0) {
            return $models;
        }

        $relation = \App\Src\Orm\Relations\Relation::noConstraints(function () use ($name) {
            return $this->getModel()->$name();
        });

        if (null !== $constraint) {
            $constraint($relation);
        }        

        foreach ($this->with as $with => $constraint) {
            if (strstr($with, '.') && substr($with, 0, strlen($name . '.')) === $name . '.') {
                $relation->getRelatedModelQuery()->with(substr($with, strlen($name . '.')));
            }
        }

        return $relation->load($name, $models);
    }

}
