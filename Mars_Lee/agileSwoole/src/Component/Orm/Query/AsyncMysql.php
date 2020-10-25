<?php

namespace Component\Orm\Query;

use Kernel\AgileCore;

class AsyncMysql extends Mysql implements IQuery
{
	const INSERT = 'INSERT';
	const SELECT = 'SELECT';
	const UPDATE = 'UPDATE';
	const DELETE = 'DELETE';

    /**
     * @var \Component\Orm\Connection\AsyncMysql
     */
	protected $connection;
	public function __construct()
	{
        /* @var \Component\Orm\Pool\ConnectionPool pool */
	    $pool = AgileCore::getInstance()->get('pool');
		$this->connection = $pool->getConnection('async');
    }

	public function execute(): string
	{
		$query     = $this->__toString();
		$bind      = array_merge($this->_values, $this->_bind);
		$statement = $this->connection->prepare($query);
		if(!$statement) {
		    throw new \Exception('sql prepare error!');
        }
	
		$statement->execute($bind);

		$this->_reset();

		if($this->_type===static::INSERT) {
			return strval($this->connection->insert_id);
		} else {
			return strval($statement->affected_rows);
		}
	}

	public function fetchAll(bool $object = false) : array
	{
		$query     = $this->__toString();

        /**
         * @var \Swoole\Coroutine\MySQL\Statement
         */
		$statement = $this->connection->prepare($query);
        $result = $statement->execute($this->_bind);

		//$result = $statement->fetchAll($fetch);

		$this->_reset();

		if(!isset($result[0])) {
			return [];
		}
		return $result;
	}

	protected function _reset(){
        $this->connection->setDefer(false);
	    parent::_reset();
	    //$this->connection->setDefer(true);
    }
}