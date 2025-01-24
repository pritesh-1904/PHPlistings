<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Dbal;

class QueryBuilder
{

    public $types = [
        0 => 'SELECT',
        1 => 'INSERT',
        2 => 'MERGE',
        3 => 'UPDATE',
        4 => 'DELETE',
        5 => 'ALTER',
    ];

    public $connection;

    public $type;
    public $alias;
    public $table;
    public $columns = [];
    public $alterColumns = [];
    public $keys;
    public $values = [];
    public $joins = [];
    public $wheres = [];
    public $orders = [];
    public $groups = [];
    public $havings = [];
    public $offset;
    public $limit;

    public function __construct(\App\Src\Dbal\Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function setConnection(\App\Src\Dbal\Connection $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    public function setTable($table, $alias = null)
    {
        $this->from($this->getConnection()->getPrefix() . $table, $alias);

        return $this;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function select($columns = null)
    {
        $this->type = 0;
        if (null !== $columns) {
            if (is_array($columns)) {
                foreach ($columns as $column) {
                    $this->columns[] = $column;
                }
            } else {
                $this->columns[] = $columns;
            }
        }

        return $this;
    }

    public function insert(array $values = null)
    {
        $this->type = 1;

        if (null !== $values) {
            if (is_array($values)) {
                foreach ($values as $key => $value) {
                    $this->values[$key] = $value;
                }
            } else {
                $this->values[] = $values;
            }
        }

        return $this->execute();
    }

    public function merge(array $values = null, $keys = null, $table = null)
    {
        $this->type = 2;

        if (null !== $values) {
            if (is_array($values)) {
                foreach ($values as $key => $value) {
                    $this->values[$key] = $value;
                }
            } else {
                $this->values[] = $values;
            }
        }

        $this->keys = $keys ?? 'id';

        $this->table = $table ?? $this->table;

        return $this->execute();
    }

    public function update($values = null)
    {
        $this->type = 3;

        if (null !== $values) {
            if (is_array($values)) {
                foreach ($values as $key => $value) {
                    $this->values[$key] = $value;
                }
            } else {
                $this->values[] = $values;
            }
        }

        return $this->execute();
    }

    public function delete()
    {
        $this->type = 4;

        return $this->execute();
    }

    public function alter()
    {
        $this->type = 5;

        return $this;
    }

    public function getType()
    {
        return (isset($this->type)) ? $this->types[$this->type] : null;
    }

    public function from($table, $alias = null)
    {
        $this->table = $table;

        if (null !== $alias) {
            $this->alias = $alias;
        }

        return $this;
    }

    public function join($table, $on, $type = 'left', $alias = null)
    {
        $this->joins[] = [$table, $on, $type, $alias];

        return $this;
    }

    public function leftJoin($table, $on, $alias = null)
    {
        return $this->join($table, $on, 'left', $alias);
    }

    public function rightJoin($table, $on, $alias = null)
    {
        return $this->join($table, $on, 'right', $alias);
    }

    public function innerJoin($table, $on, $alias = null)
    {
        return $this->join($table, $on, 'inner', $alias);
    }

    public function crossJoin($table, $on, $alias = null)
    {
        return $this->join($table, $on, 'cross', $alias);
    }

    public function addColumn($column, $definition)
    {
        $this->type = 5;

        $this->alterColumns[] = ['add', $column, $definition];

        return $this->execute();
    }

    public function dropColumn($column)
    {
        $this->type = 5;

        $this->alterColumns[] = ['drop', $column];

        return $this->execute();
    }

    public function alterColumn($column, $definition)
    {
        $this->type = 5;

        $this->alterColumns[] = ['alter', $column, $definition];

        return $this->execute();
    }

    public function renameColumn($old, $new)
    {
        $this->type = 5;

        $this->alterColumns[] = ['rename', $old, $new];

        return $this->execute();
    }

    public function where($column, $operator = null, $value = null, $attribute = 'and')
    {              
        $this->wheres[] = [$column, $operator, $value, $attribute];

        return $this;
    }

    public function orWhere($column, $operator = null, $value = null)
    {      
        return $this->where($column, $operator, $value, 'or');
    }

    public function whereIn($column, $value, $attribute = 'and')
    {      
        return $this->where($column, 'in', $value, $attribute);
    }

    public function orWhereIn($column, $value)
    {      
        return $this->whereIn($column, $value, 'or');
    }

    public function whereNotIn($column, $value, $attribute = 'and')
    {      
        return $this->where($column, 'notin', $value, $attribute);
    }

    public function orWhereNotIn($column, $value)
    {      
        return $this->whereNotIn($column, $value, 'or');
    }

    public function whereBetween($column, array $value, $attribute = 'and')
    {      
        return $this->where($column, 'between', $value, $attribute);
    }

    public function orWhereBetween($column, array $value)
    {      
        return $this->whereBetween($column, $value, 'or');
    }

    public function whereNull($column, $attribute = 'and')
    {      
        return $this->where($column, 'null', true, $attribute);
    }

    public function orWhereNull($column)
    {      
        return $this->whereNull($column, 'or');
    }

    public function whereNotNull($column, $attribute = 'and')
    {      
        return $this->where($column, 'notnull', true, $attribute);
    }

    public function orWhereNotNull($column)
    {      
        return $this->whereNotNull($column, 'or');
    }

    public function whereExists($column, $attribute = 'and')
    {      
        return $this->where($column, 'exists', true, $attribute);
    }

    public function orWhereExists($column)
    {      
        return $this->whereExists($column, 'or');
    }

    public function orderBy($column, $direction = 'asc')
    {
        $this->orders[] = [$column, $direction];

        return $this;
    }

    public function groupBy($group)
    {      
        $this->groups[] = $group;

        return $this;
    }

    public function having($having)
    {
        $this->havings[] = $having;

        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function expr()
    {
        return new ExpressionFactory;
    }

    public function raw($query, array $parameters = [])
    {
        return new Raw($query, $parameters);
    }

    //

    public function get(array $columns = null)
    {        
        if (null === $columns) {
            $columns = [$this->getTable() . '.*'];
        }

        return $this->fetchObjects($columns, new Collection());
    }

    public function first(array $columns = null)
    {        
        if (null === $columns) {
            $columns = [$this->getTable() . '.*'];
        }

        return $this->fetchObject($columns);
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

    public function count()
    {
        $query = clone $this;
        $query->orders = [];

        $subQuery = $this->getConnection()->getDriver()->buildQuery($query);

        return $this
            ->statement('SELECT COUNT(*) FROM (' . $subQuery . ') AS aggregate', $subQuery->getParameters())
            ->fetchColumn();
    }

    public function min($column)
    {
        $query = clone $this;
        $query->columns = [$query->expr()->min($column, 'aggregate')];
        $query->orders = [];

        return $query->fetchColumn();
    }

    public function max($column)
    {
        $query = clone $this;
        $query->columns = [$query->expr()->max($column, 'aggregate')];
        $query->orders = [];

        return $query->fetchColumn();
    }

    public function sum($column)
    {
        $query = clone $this;
        $query->columns = [$query->expr()->sum($column, 'aggregate')];
        $query->orders = [];

        return $query->fetchColumn();
    }

    public function avg($column)
    {
        $query = clone $this;
        $query->columns = [$query->expr()->avg($column, 'aggregate')];
        $query->orders = [];

        return $query->fetchColumn();
    }

    public function toSql()
    {
        return $this->getConnection()->getDriver()->buildQuery($this);
    }

    protected function fetchColumn()
    {
        if (false !== $result = $this->statement($this)->fetchColumn()) {
            return $result;
        }

        return null;
    }

    protected function fetchObject(array $columns = null, string $className = null, array $defaults = [])
    {
        if (false !== $result = $this->statement($this->select($columns))->fetchObject($className ?? '\App\Src\Dbal\Row', $defaults)) {
            return $result;
        }

        return null;
    }

    protected function fetchObjects(array $columns = null, \App\Src\Support\BaseCollection $collection, string $className = null, $defaults = [])
    {
        return $collection->collect(
            $this->statement($this->select($columns))->fetchAll(\PDO::FETCH_CLASS, $className ?? '\App\Src\Dbal\Row', $defaults)
        );
    }

    public function execute()    
    {
        return ($this->statement($this)->errorCode() === '00000');
    }

    public function statement($query, array $params = [])
    {
        if ($query instanceof \App\Src\Dbal\QueryBuilder) {
            $query = $this->getConnection()->getDriver()->buildQuery($query);
            $params = $query->getParameters();
        }

        $statement = $this->getConnection()->getHandler()->prepare($query);

        if (false === $statement || false === $statement->execute($params)) {
            throw new \Exception('Preparation/Execution of the PDO statement failed');
        }

        return $statement;
    }

}
