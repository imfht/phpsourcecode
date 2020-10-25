<?php

namespace Component\Orm\Query;

use Kernel\AgileCore;

class Mysql implements IQuery
{
	protected $_database  = null;
	protected $_table     = null;
	protected $_key       = null;

	protected $_state     = 0;
	protected $_record    = false;
	protected $_type      = null;

	protected $_columns   = '*';
	protected $_condition = '1';
	protected $_bind      = [];
	protected $_group     = '';
	protected $_having    = '';
	protected $_order     = [];
	protected $_offset    = 0;
	protected $_count     = 20;

	protected $_fields    = [];
	protected $_values    = [];

	const INSERT = 'INSERT';
	const SELECT = 'SELECT';
	const UPDATE = 'UPDATE';
	const DELETE = 'DELETE';

	protected $connection;
	protected $driver = 'pdo';
	public function __construct()
	{
		$this->connection = AgileCore::getInstance()->get('pool')->getConnection($this->driver);
	}

	public function insert(array $data, string $table): IQuery
	{
		if($this->_state >= 1) { throw new \LogicException('syntax error'); }

		$this->_table  = $table;
		$this->_type   = static::INSERT;
		$this->_fields = array_keys($data);
		$this->_values = array_values($data);
		$this->_state  = 7;

		return $this;
	}

	public function delete(array $condition = []): IQuery
	{
		if($this->_state >= 1) { throw new \LogicException('syntax error'); }

		$this->_type = static::DELETE;

		if(isset($condition[$this->_key])) {
			return $this->where(strval($condition[$this->_key]));
		}

		$this->_state = 2;

		return $this;
	}

	public function select(string $fields = '*'): IQuery
	{
		if($this->_state >= 1) { throw new \LogicException('syntax error'); }

		if(strpos($fields, '(')!==false and preg_match('/(?:,|^|\s)(?:avg|count|max|min|sum)\s*\(/i', $fields)) {
			$this->_record  = false;
			$this->_columns = $fields;
		} else {
			$this->_record  = true;
			$this->_columns = $fields.','.$this->_key;
		}

		$this->_type  = static::SELECT;
		$this->_state = 1;

		return $this;
	}

	public function update(array $data): IQuery
	{
		if($this->_state >= 1) { throw new \LogicException('syntax error'); }

		$this->_type = static::UPDATE;

		if(isset($data[$this->_key])) {
			$key = $data[$this->_key];
			unset($data[$this->_key]);

			$this->_fields = array_keys($data);
			$this->_values = array_values($data);

			return $this->where($key);
		} else {
			$this->_fields = array_keys($data);
			$this->_values = array_values($data);
			$this->_state = 2;

			return $this;
		}
	}

	public function where(string $bind, array $conditions = null): IQuery
	{
		if($this->_state >= 3) { throw new \LogicException('syntax error'); }

		$bind  = is_null($bind) ? [] : $bind;
		$where = static::_condition($conditions, $bind);
		$this->_condition = $where['condition'];
		$this->_bind      = $where['bind'];
		$this->_state     = 3;

		return $this;
	}

	public function group(string $fields): IQuery
	{
		if($this->_record or $this->_state >= 4) {
			throw new \LogicException('syntax error');
		}

		$this->_group = "GROUP BY {$fields}";
		$this->_state = 4;

		return $this;
	}

	public function limit(int $limit, int $offset): IQuery
	{
		if($this->_state >= 7) { throw new \LogicException('syntax error'); }

		$this->_offset = $offset;
		$this->_count  = $limit;
		$this->_state  = 7;

		return $this;
	}

	public function order(string $field, string $sort = 'desc'): IQuery
	{
		if($this->_state > 6) { throw new \LogicException('syntax error'); }

		$this->_order[] = $field.' '.$sort;
		$this->_state   = 6;

		return $this;
	}

	public function having(string $condition, array $bind = null): IQuery
	{
		if($this->_state != 4) { throw new \LogicException('syntax error'); }

		$this->_bind   = is_null($bind) ? $this->_bind : array_merge($this->_bind, $bind);
		$this->_having = "HAVING {$condition}";
		$this->_state  = 5;

		return $this;
	}

	public function from(string $table, string $database = ''): IQuery
	{
		if(strpos($table, '.') !== false) {
			$arr = explode('.', $table);
			$this->_table = $arr[1];
			$this->_database = $arr[0];
		}else {
			if($database == '') {
				{ throw new \LogicException('database is null'); }
			}
			$this->_database = $database;
			$this->_table = $table;
		}
		return $this;
	}

	public function execute(): string
	{
		$query     = $this->__toString();
		$bind      = array_merge($this->_values, $this->_bind);
		$statement = $this->connection->prepare($query);
		$statement->execute($bind);
		//$statement->closeCursor();

		$this->_reset();

		if($this->_type===static::INSERT) {
		    /** @var \PDO $connection */
		    $connection = $this->connection;
			return $connection->lastInsertId();
		} else {
			return strval($statement->rowCount());
		}
	}

	public function fetch(bool $object = false) : array
	{
		$this->_offset = 0;
		$this->_count  = 1;

		$result = $this->fetchAll($object);
		return $result[0];
	}

	public function fetchAll(bool $object = false) : array
	{
		$query     = $this->__toString();

		$statement = $this->connection->prepare($query);
		$statement->execute($this->_bind);
		$fetch = \PDO::FETCH_ASSOC;
		if($object) {
			$fetch = \PDO::FETCH_CLASS;
		}
		$result = $statement->fetchAll($fetch);

		$this->_reset();

		if(!isset($result[0])) {
			return [];
		}
		return $result;
	}

	public function __toString()
	{
		switch($this->_type) {
			case static::INSERT : $query =  "INSERT INTO {$this->_table}(".join($this->_fields, ',').")VALUES(?".str_repeat(',?', count($this->_fields)-1).")";
				break;
			case static::SELECT : $query =  "SELECT {$this->_columns} FROM {$this->_table} "
				."WHERE {$this->_condition} {$this->_group} {$this->_having} "
				.(isset($this->_order[0]) ? " ORDER BY ".implode(',', $this->_order) : "")
				." LIMIT {$this->_offset},{$this->_count}";
				break;
			case static::UPDATE : $query =  "UPDATE {$this->_table} SET ".join($this->_fields, '=?,')."=? "
				."WHERE {$this->_condition} "
				.(isset($this->_order[0]) ? " ORDER BY ".implode(',', $this->_order) : "")
				." LIMIT {$this->_count}";
				break;
			case static::DELETE : $query =  "DELETE FROM {$this->_table} "
				."WHERE {$this->_condition} "
				.(isset($this->_order[0]) ? " ORDER BY ".implode(',', $this->_order) : "")
				." LIMIT {$this->_count}";
				break;
			default:
				throw new \Exception('query build error');
		}

		return $query;
	}

	protected function _condition($condition, array $bind=null)
	{
		switch(gettype($condition))
		{
			case 'string' :
				if(!ctype_alnum($condition)) {
					if(strpos($condition, '(?)')!==false and is_array($bind)) {
						$condition= str_replace('(?)', '(%s)', $condition);
						$temp     = [];
						$holders  = [];
						foreach($bind as $param) {
							if(is_array($param)) {
								$holders[] = '?'.str_repeat(',?', count($param)-1);
								$temp      = array_merge($temp, $param);
							} else {
								$temp[] = $param;
							}
						}

						$bind      = $temp;
						$condition = vsprintf($condition, $holders);
					}
					break;
				}
			case 'integer':
				$bind      = array($condition);
				$condition = $this->_key.'=?';
				break;
			case 'array'  :
				$bind      = $condition;
				$condition = $this->_key.' IN(?'.str_repeat(',?', count($bind)-1).')';
				break;
			default      :
				throw new \LogicException('syntax error');
				break;
		}

		return array('condition'=>$condition, 'bind'=>$bind);
	}

	protected function _reset()
	{
		$this->_columns  = '*';
		$this->_condition= '1';
		$this->_bind     = [];
		$this->_group    = '';
		$this->_having   = '';
		$this->_order    = [];
		$this->_offset   = 0;
		$this->_count    = 20;
		$this->_state    = 0;
		$this->_fields   = [];
		$this->_values   = [];
		$this->connection->free();
	}
}