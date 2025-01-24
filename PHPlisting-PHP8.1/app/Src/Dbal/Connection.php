<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Dbal;

class Connection
{

    protected $handler;
    protected $driver;
    protected $identifier = 'default';
    protected $prefix;

    public function __construct($identifier = null)
    {
        if (null !== $identifier) {
            $this->setIdentifier($identifier);
        }

        $this->prefix = config()->db->{$this->identifier}['prefix'];
        $this->setDriver(config()->db->{$this->identifier}['driver']);
        $this->handler = $this->getDriver()->createHandler($this->getIdentifier());
    }   

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function setDriver($driver)
    {
        $class = '\\App\\Src\\Dbal\\Driver\\' . ucfirst(strtolower($driver)) . '\\Driver';

        if (class_exists($class) && is_subclass_of($class, '\\App\\Src\\Dbal\\Driver\\DriverInterface')) {
            $this->driver = new $class;
        } else {
            throw new \Exception('Database driver "' . $driver . '" is not supported');
        }
    }

    public function getDriver()
    {
        return $this->driver;
    }

    public function table(string $table, $alias = null)
    {
        return (new \App\Src\Dbal\QueryBuilder($this))
            ->setTable($table, $alias);
    }

    public function expr()
    {
        return new ExpressionFactory;
    }

    public function raw(string $query, $parameters = [])
    {
        return new \App\Src\Dbal\Raw($query, $parameters);
    }

    public function lastInsertId()
    {
        return $this->getHandler()->lastInsertId();
    }

    public function statement($query, array $params = [])
    {
        return (new \App\Src\Dbal\QueryBuilder($this))
            ->statement($query, $params);
    }

    public function beginTransaction()
    {
        return $this->getHandler()->beginTransaction();
    }

    public function commit()
    {
        return $this->getHandler()->commit();
    }

    public function rollBack()
    {
        return $this->getHandler()->rollBack();
    }

}
