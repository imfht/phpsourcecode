<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Adm\Sql;


use Ke\Adm\Adapter\DbAdapter;
use Ke\Adm\Query;

class QueryBuilder
{
	
	const JOIN = 0;
	const LEFT_JOIN = 0b1;
	const RIGHT_JOIN = 0b10;
	const INNER_JOIN = 0b100;
	const OUTER_JOIN = 0b1000;
	
	const MARRY_AND = 'AND';
	const MARRY_OR = 'OR';
	
	const AS = ' AS ';
	const WHERE = 'WHERE ';
	const ORDER = 'ORDER BY ';
	const GROUP = 'GROUP BY ';
//	const JOIN      = 'JOIN ';
	
	const OPR_NONE_VALUE = 0;
	const OPR_SINGLE_VALUE = 1;
	const OPR_DOUBLE_VALUE = 2;
	const OPR_MULTI_VALUES = 100;
	
	/**
	 * @var array 核心的操作符
	 */
	protected static $operators = [
		'null'    => [null, self::OPR_NONE_VALUE, '%s IS NULL'],
		'!null'   => [null, self::OPR_NONE_VALUE, '%s IS NOT NULL'],
		'='       => ['in'],
		'!='      => ['!in'],
		'<>'      => ['!in'],
		'>'       => [null, self::OPR_SINGLE_VALUE,],
		'<'       => [null, self::OPR_SINGLE_VALUE,],
		'>='      => [null, self::OPR_SINGLE_VALUE,],
		'<='      => [null, self::OPR_SINGLE_VALUE,],
		'in'      => [null, self::OPR_MULTI_VALUES, '%s IN (%s)', ','],
		'!in'     => [null, self::OPR_MULTI_VALUES, '%s NOT IN (%s)', ','],
		'like'    => [null, self::OPR_SINGLE_VALUE, '%s LIKE %s'],
		'between' => [null, self::OPR_DOUBLE_VALUE, '%s BETWEEN %s', ' AND '],
	];
	
	/** @var DbAdapter */
	protected $adapter;
	
	protected $debug = false;
	
	public function __construct(DbAdapter $adapter)
	{
		$this->adapter = $adapter;
	}
	
	public function quote(string $value): string
	{
		return $this->adapter->quote($value);
	}
	
	public function buildSelect($query, string &$sql = null, array &$args = null)
	{
		if (!($query instanceof Query))
			$query = (new Query())->load($query);
		
		$isUseTablesIndex = $query->isUseTablesIndex();
		
		$this->debug = $query->debug;
		
		$space = ' ' . ($query->debug ? "\n" : null);
		// 预备阶段
		$tables = new QueryTables();
		
		// from
		$from = $query->from;
		if ($isUseTablesIndex)
			$from = $tables->addTable($query->from, true);
		
		$join = [];
		if (!empty($query->join))
			$this->prepareJoin($query, $args, $tables, $join, $query->join);
		
		$select = $this->filterSelect($query->select);
		
		if ($isUseTablesIndex)
			$from = $tables->tableName($from);
		
		
		$sql .= "SELECT {$select} FROM {$from}";
		
		if (!empty($join)) {
			$sql .= $space . implode($space, $join);
		}
		
		if (!empty($query->where)) {
			$this->buildWhere($query->where, $sql, $args);
		}
		
		if (!empty($query->group)) {
			$sql .= $space . self::GROUP . $query->group;
		}
		
		if (!empty($query->order)) {
			$this->buildOrder($query->order, $sql);
		}
		
		$this->buildLimitOffset($query->limit, $query->offset, $sql);
		$this->debug = false;
		
		return $this;
	}
	
	public function buildUpdate(string $table, array $data, $where, &$sql = null, array &$args = null)
	{
		$fields = [];
		foreach ($data as $key => $val) {
			// 这个改法还要再测试一段时间才能知道有没有副作用
			// @field + value
			// @children_count + 1
			// 暂时不支持这个特性，要重新调整符号，不使用 @
//			if (preg_match('#(?:\@([\w]+))[\s\t]*([\+\-\*\/])[\s\t]*(.*)#i', $val, $matches)) {
//				list(, $field, $symbol, $val) = $matches;
//				$fields[] = "{$key} = {$field} {$symbol} ?";
//				$args[] = $val;
//			} else {
//
//			}
			$fields[] = "{$key} = ?";
			$args[] = $val;
		}
		$sql = 'UPDATE ' . (string)$table . ' SET ' . implode(',', $fields);
		if (!empty($where))
			$this->buildWhere($where, $sql, $args);
		return $this;
	}
	
	public function buildInsert(string $table, array $data, string &$sql = null, array &$args = null)
	{
		$keys = [];
		$placeholder = [];
		$args = [];
		foreach ($data as $key => $val) {
			$keys[] = $key;
			$placeholder[] = '?';
			$args[] = $val;
		}
		$sql = 'INSERT INTO ' . (string)$table .
			' (' . implode(',', $keys) . ')' .
			' VALUES (' . implode(',', $placeholder) . ')';
		return $this;
	}
	
	public function buildDelete(string $table, $where, string &$sql = null, array &$args = null)
	{
		$sql = 'DELETE FROM ' . (string)$table;
		if (!empty($where))
			$this->buildWhere($where, $sql, $args);
		return $this;
	}
	
	protected function prepareJoin(Query $query, array &$queryArgs = null, QueryTables $tables, array & $join = null, ...$args)
	{
		if (empty($args))
			return $this;
		$isUseTablesIndex = $query->isUseTablesIndex();
		$isDebug = $query->debug;
		$queryArgs = $queryArgs ?? [];
		if (is_array($args[0])) {
			foreach ($args as $arg) {
				$this->prepareJoin($query, $queryArgs, $tables, $join, ...$arg);
			}
			return $this;
		}
		$mode = self::JOIN;
		if (is_int($args[0]))
			$mode = array_shift($args);
		$count = count($args);
		// $query = ['join' => 'join table t2 on t2.user_id = tb1.id']
		// 如果join只是一个普通的字符串，还是允许他拼接sql，只是无法进行表索引的检查了
		if ($count === 1) {
			$sql = trim($args[0]);
			if (!empty($sql)) {
				if (stripos($sql, 'JOIN ') === false)
					$sql = 'JOIN ' . $sql;
				$join[] = $sql;
			}
			return $this;
		}
		$otherOn = $count > 2 && is_array($args[$count - 1]) ? array_pop($args) : null;
		if (!empty($otherOn))
			$count = count($args);
		$joinTable = null;
		$joinField = null;
		$targetTable = null;
		$targetField = null;
		list($joinTable, $joinField) = $this->splitTableField($args[0]);
		if ($count === 2) {
			// $query->join('user_log.user_id', 'user.id')
			list($targetTable, $targetField) = $this->splitTableField($args[1]);
		} else if ($count === 3) {
			// $query->join('user_log', 'user_id', 'tb1.id')
			if (empty($joinField) && !empty($args[1]))
				$joinField = $args[1];
			list($targetTable, $targetField) = $this->splitTableField($args[2]);
		} else if ($count === 4) {
			// $query->join('user_log', 'user_id', 'tb1', 'id')
			if (empty($joinField) && !empty($args[1]))
				$joinField = $args[1];
			list($targetTable, $targetField) = $this->splitTableField($args[2]);
			if (empty($targetField) && !empty($args[3]))
				$targetField = $args[3];
		}
		if ($isUseTablesIndex) {
			$joinTableKey = "$targetTable.$targetField";
			if ((!empty($joinField) && !empty($targetField)) &&
				(!empty($joinTable) && ($joinTable = $tables->addTable($joinTable, false, $joinTableKey)) !== false) &&
				(!empty($targetTable) && ($targetTable = $tables->tableOf($targetTable)) !== false)
			) {
				$sql = $this->getJoinStr($mode) . ' ' . $tables->tableName($joinTable) . ' ON ' .
					$tables->tableField($joinTable, $joinField) . ' = ' .
					$tables->tableField($targetTable, $targetField);
				$join[] = $sql;
			}
		} else {
			if (!empty($joinField) && !empty($targetField) && !empty($targetTable)) {
				$sql = $this->getJoinStr($mode) . ' ' . $joinTable . ' ON ' . $joinField . ' = ' . $targetTable . '.' . $targetField;
				if (!empty($otherOn)) {
					foreach ($otherOn as $field => $value) {
						if (is_null($value)) {
							$sql .= " AND {$field} IS NULL";
						} else {
							if ($isDebug) {
								$sql .= " AND {$field} = '{$value}'";
							} else {
								$sql .= " AND {$field} = ?";
								$queryArgs[] = $value;
							}
						}
					}
				}
				$join[] = $sql;
			}
		}
		
		return $join;
	}
	
	
	protected function filterSelect($args)
	{
		if (empty($args))
			return '*';
		$select = [];
		if (is_string($args))
			$select[] = $args;
		else if (is_array($args)) {
			array_walk_recursive($args, function ($field) use (&$select) {
				if (!empty($field = trim($field)))
					$select[] = $field;
			});
		}
		return empty($select) ? '*' : implode(',', $select);
	}
	
	public function buildWhere($where, string & $sql = null, array & $args = null)
	{
		if (empty($where))
			return $this;
		$space = ' ';
		if ($this->debug)
			$space .= "\n";
		if (is_string($where)) {
			$where = trim($where);
			if (!empty($where)) {
				if (stripos($where, self::WHERE) === 0)
					$sql .= $space . $where;
				else
					$sql .= $space . self::WHERE . $where;
			}
		} else if (is_array($where)) {
			$group = [];
			$this->pushWhere($where, null, 0, $group, $args);
			if (!empty($group) && !empty($group[1])) {
				$group[0] = 'WHERE';
				$sql .= $space . implode(' ', $group);
			}
		}
		return $this;
	}
	
	protected function pushWhere(array $where, $marry = null, $deep = 0, array &$group = [], array & $params = null)
	{
		if ($where[0] === self::MARRY_AND || $where[0] === self::MARRY_OR) {
			$marry = array_shift($where);
		}
		if (empty($marry))
			$marry = self::MARRY_AND;
		if (isset($where[0]) && is_array($where[0])) {
			$passMarry = $marry;
			$newGroup = [];
			foreach ($where as $index => $item) {
				if ($item === self::MARRY_AND || $item === self::MARRY_OR) {
					$passMarry = $item;
					continue;
				}
				if (is_array($item)) {
					if ($passMarry !== null) {
						if ($item[0] === self::MARRY_AND || $item[0] === self::MARRY_OR)
							$item[0] = $passMarry;
						else
							array_unshift($item, $passMarry);
						$passMarry = null;
					}
					$this->pushWhere($item, $passMarry, $deep + 1, $newGroup, $params);
				}
			}
//			$group[] = $newGroup;
			if (count($newGroup) > 2) {
				$group[] = array_shift($newGroup);
//				$group[] = '(' . implode(' ', $newGroup) . ')';
				if ($deep > 0) {
					$group[] = '(' . implode(' ', $newGroup) . ')';
				} else {
					$group[] = implode(' ', $newGroup);
				}
			} else {
				foreach ($newGroup as $item)
					$group[] = $item;
			}
		} else {
			$count = count($where);
			if ($count < 2)
				return $this; // break
			list($field, $operator) = $where;
			
			$sql = '';
			$debug = $this->debug;

//			$sql = $field . ' ';
//			$params = &$params;
			$values = [];
			
			if (is_array($operator) || is_object($operator)) {
				$values = (array)$operator;
				$operator = 'in';
				$where = [$field, $operator, $values];
			}
			if (!isset(static::$operators[$operator])) {
				array_shift($where);
				$operator = 'in';
				$where = [$field, $operator, $where];
			}
			if (isset(static::$operators[$operator])) {
				$holder = '';
				// 已知操作符
				$options = static::$operators[$operator];
				if (isset($options[0]) && isset(static::$operators[$options[0]]))
					$options = static::$operators[$options[0]];
				if (!isset($options[1]))
					return $this; // break
				// 构造基本的SQL
				if ($options[1] !== self::OPR_NONE_VALUE) {
					$limit = $options[1];
					if ($limit === self::OPR_MULTI_VALUES)
						$values = array_slice($where, 2);
					else
						$values = array_slice($where, 2, $options[1]); // 尽可能减少后面flatten的数量
					if (empty($values))
						$values = [null]; // 如果指定了where查询，不能让他没有查询这个字段
					$counter = 0;
					array_walk_recursive($values,
						function ($value) use (&$holder, &$params, &$counter, &$limit, $debug) {
							if ($limit === self::OPR_MULTI_VALUES || $counter < $limit) {
								if ($this->debug) {
									if ($value instanceof Query) {
										$limit = self::OPR_SINGLE_VALUE;
										$value = '(' . $value->sql() . ')';
									} else if ($value === null) {
										$value = "''";
									} else {
										if (!is_string($value)) {
											if ($value instanceof \stdClass)
												$value = 'stdClass';
											else
												$value = (string)$value;
										}
										$value = $this->quote($value);
									}
									
									$holder .= $value . ',';
								} else {
									if ($value instanceof Query) {
										$limit = self::OPR_SINGLE_VALUE;
										$value->getQueryBuilder()->buildSelect($value, $subSql, $subParams);
										if (!empty($subSql)) {
											$holder .= '(' . $subSql . '),';
											if (!empty($subParams)) {
												foreach ($subParams as $subParam) {
													$params[] = $subParam === null ? '' : $subParam;
												}
											}
										}
									} else {
										$holder .= '?,';
										$params[] = $value === null ? '' : $value;
									}
								}
								$counter++;
							}
						});
					$holder = substr($holder, 0, -1);
				}
				// 构建sql
				if (!empty($options[2])) {
					$sql = sprintf($options[2], $field, $holder);
				} else {
					$sql = "{$field} {$operator}";
					if (!empty($holder))
						$sql .= ' ' . $holder;
				}
			} else {
				// todo: 未知操作符，暂时没做处理
			}
			$group[] = $marry;
			$group[] = $sql;
		}
		return $group;
	}
	
	public function buildIn(array $keyValues, $marry = self::MARRY_AND, $notIn = false)
	{
		$args = [$marry];
		$opr = $notIn ? '!in' : 'in';
		foreach ($keyValues as $key => $values) {
			$args[] = [$key, $opr, $values];
		}
		return $args;
	}
	
	public function buildLimitOffset(int $limit = 0, int $offset = -1, string & $sql = null)
	{
		$space = ' ';
		if ($this->debug)
			$space .= "\n";
		if ($limit > 0) {
			$sql .= $space;
			if ($offset >= 0)
				$sql .= 'LIMIT ' . $offset . ',' . $limit;
			else
				$sql .= 'LIMIT ' . $limit;
		}
	}
	
	public function buildOrder($order, string &$sql = null)
	{
		if (empty($order))
			return $this;
		
		$space = ' ';
		if ($this->debug)
			$space .= "\n";
		if (is_string($order)) {
			$order = trim($order);
			if (!empty($order)) {
				if (stripos($order, self::ORDER) === 0)
					$sql .= $space . $order;
				else
					$sql .= $space . self::ORDER . $order;
			}
		} else if (is_array($order)) {
			$order = $this->pushOrder(...$order);
			if (!empty($order)) {
				$sql .= $space . self::ORDER . implode(',', $order);
			}
		}
		return $this;
	}
	
	protected function pushOrder(...$args)
	{
		if (empty($args))
			return [];
		$order = [];
		$mode = 0;
		foreach ($args as $arg) {
			if (is_array($arg)) {
				$count = count($arg);
				$lastIndex = $count - 1;
				if (is_int($arg[$lastIndex]))
					$mode = array_pop($arg);
				if (is_array($arg[0]))
					$order[] = $this->pushOrder(...$arg);
				else if (!empty($arg))
					$order[] = implode(',', $arg) . $this->getOrderModeStr($mode);
			} else if (is_string($arg))
				$order[] = $arg;
		}
		return $order;
	}
	
	protected function splitTableField(string $field, int $index = null)
	{
		$split = explode('.', $field);
		if (!isset($split[1]))
			$split[1] = null;
		return $split;
	}
	
	protected function getJoinStr(int $type)
	{
		switch ($type) {
			case self::LEFT_JOIN :
				return 'LEFT JOIN';
			case self::RIGHT_JOIN :
				return 'RIGHT JOIN';
			case self::INNER_JOIN :
				return 'INNER JOIN';
			case self::OUTER_JOIN :
				return 'OUTER JOIN';
			default :
				return 'JOIN';
		}
	}
	
	protected function getOrderModeStr(int $mode)
	{
		if ($mode > 0)
			return ' ASC';
		else if ($mode < 0)
			return ' DESC';
		else
			return '';
	}
}