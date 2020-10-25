<?php

namespace Component\Orm\Query;


use Component\Orm\Query\Mongodb\Parser;
use Component\Orm\Query\Mongodb\Tokenizer;
use MongoDB\BSON\ObjectID;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Command;
use MongoDB\Driver\Query;
use Component\Orm\Connection\Mongodb as MongoConnection;


class Mongodb implements IQuery
{
	private $_database  = null;
	private $_table     = null;

	private $_state     = 0;
	private $_type      = null;

	private $_columns   = null;
	private $_condition = '_id is not null';
	private $_bind      = [];
	private $_aggregate = null;
	private $_distinct  = null;
	private $_group     = '';
	private $_having    = '';
	private $_order     = [];
	private $_offset    = 0;
	private $_count     = 20;

	private $_data      = [];
	private $_record    = true;

	const INSERT = 'INSERT';
	const SELECT = 'SELECT';
	const UPDATE = 'UPDATE';
	const DELETE = 'DELETE';
	protected $connection;
	public function __construct(MongoConnection $mongodb)
	{
		$this->connection = $mongodb;
	}

	public function from(string $table, string $database = '') : IQuery
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

	public function insert(array $data, string $table): IQuery
	{
		$this->from($table);
		if($this->_state >= 1) { throw new \LogicException('syntax error'); }

		if(!isset($data['_id'])) {
			$data['_id'] = new ObjectID();
		}

		$this->_type = self::INSERT;
		$this->_data = $data;
		$this->_state= 7;

		return $this;
	}

	public function delete(array $data = []): IQuery
	{
		if($this->_state >= 1) { throw new \LogicException('syntax error'); }

		$this->_type = static::DELETE;

		if(isset($data['_id'])) {
			return $this->where('_id=?', array($data['_id']));
		}

		$this->_state = 2;

		return $this;
	}

	public function select(string $columns = '*'): IQuery
	{
		if($this->_state >= 1) { throw new \LogicException('syntax error', 2001); }

		if(strpos($columns, '(')!==false and preg_match_all('/(?:,|^|\s)(avg|count|max|min|sum|distinct)\s*\(([^\(\)]+)\)\s*(?:as\s+([a-z0-9_]+))?/i', $columns, $matches)) {
			$aggregate = [];
			foreach($matches[1] as $key=>$function) {
				$field = $matches[2][$key];
				$as    = empty($matches[3][$key]) ? $function : $matches[3][$key];
				if($function==='count') {
					$aggregate[$as] = array('$sum'=>1);

				} elseif($function==='distinct') {
					$aggregate[$as]  = array('$sum'=>1);
					$this->_group    = array($field=>'$'.$field);
					$this->_distinct = $field;

				} else {
					$aggregate[$as] = array("\${$function}"=>"\${$field}");
				}
			}

			$this->_aggregate = $aggregate;
			$this->_record    = false;

		} else {
			$this->_record    = true;
			if($columns!=='*') {
				$fields = explode(',', $columns);
				$this->_columns = [];
				foreach($fields as $field) {
					$this->_columns[$field] = true;
				}
			}
		}

		$this->_type  = static::SELECT;
		$this->_state = 1;

		return $this;
	}

	public function update(array $data): IQuery
	{
		if($this->_state >= 1) { throw new \LogicException('syntax error'); }

		$this->_type = static::UPDATE;

		if(isset($data['_id'])) {
			$key = $data['_id'];
			unset($data['_id']);

			$this->_data = $data;

			return $this->where('_id=?', array($key));
		} else {
			$this->_data  = $data;
			$this->_state = 2;

			return $this;
		}
	}

	public function where(string $where, array $conditions): IQuery
	{
		if($this->_state >= 3) { throw new \LogicException('syntax error'); }

		$bind  = is_null($conditions) ? [] : $conditions;
		$where = static::_condition($where, $bind);
		$this->_condition = $where['condition'];
		$this->_bind      = $where['bind'];
		$this->_state     = 3;

		return $this;
	}

	public function group(string $fields): IQuery
	{
		if($this->_record or $this->_distinct or $this->_state >= 4) {
			throw new \LogicException('syntax error');
		}

		$group  = [];
		$fields = explode(',', $fields);
		foreach($fields as $field) {
			$group[$field] = '$'.$field;
		}

		$this->_group = $group;
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

		$this->_order[$field] = strtolower($sort)==='asc' ? 1 : -1;
		$this->_state         = 6;

		return $this;
	}

	public function having(string $condition, array $bind = null): IQuery
	{
		if($this->_state != 4) { throw new \LogicException('syntax error'); }

		$where = static::_condition($condition, $bind);
		$this->_having = $where['condition'];
		$this->_bind   = is_null($bind) ? $this->_bind : array_merge($this->_bind, $bind);
		$this->_state  = 5;

		return $this;
	}

	public function fetch(bool $object = false) : array
	{
		$this->_offset = 0;
		$this->_count  = 1;

		$result = $this->fetchAll($object);

		return $result[0] ?? [];
	}

	public function fetchAll(bool $object = false) : array
	{

		$manager    = $this->connection->getManager();
		$collection = $this->_database.'.'.$this->_table;
		$tree       = $this->_parse($this->_condition);
		$where      = $this->_bind($tree, $this->_bind);
		$result     = [];

		if($this->_record) {
			$options = [];
			if($this->_columns) {
				$options['projection'] = $this->_columns;
			}
			$options['skip']  = $this->_offset;
			$options['limit'] = $this->_count;
			$options['sort']  = $this->_order;
			$query  = new Query($where, $options);
			$cursor = $manager->executeQuery($collection, $query)->toArray();

			foreach($cursor as $row) {
				$row = (array)$row;
				if(isset($row['_id'])) {
					$row['_id'] = strval($row['_id']);
				}
				$result[] = $this->_stdToArray($row);
			}

		} elseif($this->_aggregate) {
			$ops = array(array('$match'=>$where));

			$this->_aggregate['_id'] = $this->_group ? $this->_group : null;
			$ops[] = array('$group'=>$this->_aggregate);

			if($this->_having) {
				$tree   = $this->_parse($this->_having);
				$having = $this->_bind($tree, $this->_bind);
				$ops[]  = array('$match'=>$having);
			}

			if(!empty($this->_order)) {
				$ops[] = array('$sort'=>$this->_order);
			}

			$ops[] = array('$skip' =>$this->_offset);
			$ops[] = array('$limit'=>$this->_count);

			$command = new Command(array(
				'aggregate' => $this->_table,
				'pipeline'  => $ops,
				'cursor'    => new \stdClass,
			));

			$cursor = $manager->executeCommand($this->_database, $command)->toArray();
			if($this->_group) {
				foreach($cursor as $key=>$row) {
					$row = (array)$row;
					$row = array_merge((array)$row['_id'], $row);
					unset($row['_id'], $row['distinct']);
					$result[$key] = $this->_stdToArray($row);
				}
			} else {
				foreach($cursor as $key=>$row) {
					$row = (array)$row;
					unset($row['_id']);
					$result[$key] = $this->_stdToArray($row);
				}
			}

		} else {
			throw new \LogicException('syntax error');
		}

		$this->_reset();

		if(!isset($result[0])) {
			return [];
		} elseif($object===false) {
			return $result;
		}else{
			return $this->_toObject($result);
		}
	}


	public function execute($object = false) : string
	{
		switch($this->_type) {
			case static::INSERT :
				$result = $this->_insert();
				break;
			case static::UPDATE :
			case static::DELETE :
				$result = $this->_execute();
				break;
			default             :
				$result = '0';
		}

		$this->_reset();
		return $result;
	}

	private function _stdToArray($result)
	{
		$array = [];
		foreach($result as $key=>$value) {
			if(is_object($value)) {
				switch(get_class($value)) {
					case 'MongoDB\BSON\ObjectID'   :
						$value = strval($value);
						break;
					case 'stdClass'                :
						$value = $this->{__FUNCTION__}($value);
						break;
					case 'MongoDB\BSON\Timestamp'  :
						$time  = strval($value);
						$value = intval(substr($time, strpos($time, ':')+1, -1));
						break;
					case 'MongoDB\BSON\UTCDateTime':
						$value = strval($value);
						break;
					case 'MongoDB\BSON\Regex'      :
						$value = strval($value);
						break;
					case 'MongoDB\BSON\Binary'     :
						/* @var \MongoDB\BSON\Binary  $value*/
						$value = $value->getData();
						break;
				}
			}

			$array[$key] = $value;
		}

		return $array;
	}

	protected function _insert() : string
	{
		$manager    = $this->connection->getManager();
		$collection = $this->_database.'.'.$this->_table;
		$bulk       = new BulkWrite();

		$bulk->insert($this->_data);
		$manager->executeBulkWrite($collection, $bulk);

		$result = $this->_data['_id'];
		return strval($result);
	}

	protected function _execute() : string
	{
		$manager    = $this->connection->getManager();
		$collection = $this->_database.'.'.$this->_table;
		$bulk       = new BulkWrite();

		$tree     = $this->_parse($this->_condition);
		$criteria = $this->_bind($tree, $this->_bind);
		$query    = new Query($criteria, array(
			'projection' => array('_id'=>1),
			'skip'       => 0,
			'limit'      => $this->_count,
		));
		$cursor   = $manager->executeQuery($collection, $query)->toArray();

		if(count($cursor)<1) {
			return '0';
		}
		$keys = [];
		foreach($cursor as $row) {
			$keys[] = $row->_id;
		}
		$criteria = array('_id'=>array('$in'=>$keys));
		if($this->_type == self::UPDATE) {
			$bulk->update($criteria, array('$set' => $this->_data), array('multi' => true));
			$result = $manager->executeBulkWrite($collection, $bulk)->getModifiedCount();
		}else{
			$bulk->delete($criteria, array('limit'=>0));
			$result = $manager->executeBulkWrite($collection, $bulk)->getDeletedCount();
		}
		return strval($result);
	}

	protected function _parse($condition)
	{
		return Parser::parse(Tokenizer::tokenize($condition));
	}

	protected function _bind(array &$tree, array &$bind=null)
	{
		foreach($tree as $key=>$conds) {
			if($key==='_id') {
				$value = array_shift($bind);
				if(is_string($value) and strlen($value)===24) {
					$value = new ObjectID($value);
				} elseif(is_array($value)) {
					foreach($value as $index=>$id) {
						if(is_string($id) and strlen($id)===24) {
							$value[$index] = new ObjectID($id);
						}
					}
				}
				array_unshift($bind, $value);
			}

			if(is_array($conds)) {
				$tree[$key] = $this->{__FUNCTION__}($conds, $bind);
			} elseif($conds==='?') {
				$value = array_shift($bind);
				if($value===null) { throw new \InvalidArgumentException('SQL parameter is missing'); }
				if($key==='$like') {
					unset($tree[$key]);
					$head   = substr($value, 0, 1);
					$tail   = substr($value, -1);
					$middle = substr($value, 1, -1);
					$head   = $head==='%' ? '' : '^'.$head;
					$tail   = $tail==='%' ? '' : $tail.'$';
					$middle = str_replace('%', '.+', $middle);
					$middle = str_replace('_', '.', $middle);
					$value  = $head.$middle.$tail;
					$key    = '$regex';
				} elseif($key==='$near') {
					if(!is_array($value) and count($value)>1) {
						throw new \LogicException('syntax error');
					}

					$longitude = floatval(array_shift($value));
					$latitude  = floatval(array_shift($value));
					$distance  = count($value)===0 ? 2000 : intval(array_shift($value));
					$value     = array(
						'$geometry'   => array(
							'type'        => 'Point',
							'coordinates' => array($longitude, $latitude)
						),
						'$maxDistance'=> $distance,
					);
				}
				$tree[$key] = $value;
			} elseif($key==='$exists') {
				continue;
			} else {
				throw new \LogicException('syntax error');
			}
		}

		return $tree;
	}

	private function _reset()
	{
		$this->_columns  = null;
		$this->_condition= '_id is not null';
		$this->_bind     = [];
		$this->_aggregate= null;
		$this->_distinct = null;
		$this->_group    = '';
		$this->_having   = '';
		$this->_order    = [];
		$this->_offset   = 0;
		$this->_count    = 20;
		$this->_state    = 0;
		$this->_data     = [];
        $this->connection->free();
	}

	protected function _condition($condition, array $bind)
	{
		switch(gettype($condition))
		{
			case 'string' :
				if(!ctype_alnum($condition)) {
					break;
				}
			case 'integer':
				$bind      = array($condition);
				$condition = '_id=?';
				break;
			case 'array'  :
				$bind      = array($condition);
				$condition = '_id IN(?)';
				break;
			default      :
				throw new \LogicException('syntax error');
				break;
		}

		return array('condition'=>$condition, 'bind'=>$bind);
	}

	protected function _toObject(array $result)
	{
		return new Class($result) implements \Countable,\Iterator,\ArrayAccess {
			private $_query   = null;
			private $_data    = null;
			private $_position = 0;

			public function __construct(array $data, query $query=null)
			{
				$this->_data  = $data;
				$this->_query = $query;
			}

			public function offset(int $row=0, int $offset=0)
			{
				if(isset($this->_data[$row]) and count($this->_data[$row])>$offset) {
					$values = array_values($this->_data[$row]);
					return $values[$offset];
				}

				return null;
			}

			public function column(string $column=null, string $index=null):array
			{
				return array_column($this->_data, $column, $index);
			}


			public function toArray():array
			{
				return $this->_data;
			}

			//Countable
			public function count()
			{
				return count($this->_data);
			}


			//Iterator
			public function current()
			{
				if($this->valid()) {
					return $this->_data[$this->_position];
				}
				throw new \Exception('the position '.$this->_position.' has no data');
			}

			public function key()
			{
				return $this->_position;
			}

			public function next()
			{
				++$this->_position;
			}

			public function rewind()
			{
				$this->_position = 0;
			}

			public function valid()
			{
				return isset($this->_data[$this->_position]);
			}

			//ArrayAccess
			public function offsetSet($offset, $value)
			{
				if(is_null($offset)) {
					$this->_data[] = $value;
				} else {
					$this->_data[$offset] = $value;
				}
			}

			public function offsetExists($offset)
			{
				return isset($this->_data[$offset]);
			}

			public function offsetUnset($offset)
			{
				unset($this->_data[$offset]);
			}

			public function offsetGet($offset)
			{
				if(!isset($this->_data[$offset])) {
					return null;
				}

				return $this->_data[$offset];
			}

		};
	}
}