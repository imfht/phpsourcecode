<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Adm;


use Ke\Adm\Adapter\DbAdapter;

class Query
{
	
	private $model = null;
	
	private $source = null;
	
	private $isUserTablesIndex = true;
	
	public $debug = false;
	
	public $prefix = null;
	
	public $select = [];
	
	public $from = false;
	
	public $join = [];
	
	public $where = [];
	
	public $order = [];
	
	public $group = false;
	
	public $limit = -1;
	
	public $offset = -1;
	
	/** @var Pagination */
	protected $pagination = null;

	public function __construct(string $source = null)
	{
		$this->source($source);
	}

	public function setModel(string $class)
	{
		if (class_exists($class) && is_subclass_of($class, Model::class)) {
			/** @var Model $class */
			$this->model = $class;
			$this->source($class::getDbSource());
			if (empty($this->from))
				$this->from = $class::getTable();
		}
		return $this;
	}
	
	public function getModel()
	{
		return $this->model;
	}
	
	public function setUseTablesIndex(bool $isUse)
	{
		$this->isUserTablesIndex = $isUse;
		return $this;
	}
	
	public function isUseTablesIndex()
	{
		return $this->isUserTablesIndex;
	}
	
	public function source(string $source = null)
	{
		if (empty($source))
			$source = Db::DEFAULT_SOURCE;
		$this->source = $source;
		return $this;
	}
	
	public function debug(bool $debug)
	{
		$this->debug = $debug;
		return $this;
	}
	
	public function load($query)
	{
		if (is_array($query)) {
			$isPage = false;
			foreach ($query as $key => $value) {
				if (!$isPage && $key === 'page') {
					$isPage = true;
					$this->paginate($value);
					continue;
				}
				$this->$key = $value;
			}
		}
		return $this;
	}
	
	public function getAdapter()
	{
		if (isset($this->model)) {
			/** @var Model $class */
			$class = $this->model;
			$class::getDbAdapter();
		}
		return Db::getAdapter($this->source);
	}
	
	public function getQueryBuilder()
	{
		return $this->getAdapter()->getQueryBuilder();
	}
	
	/**
	 * @return array|DataList
	 */
	public function find(bool $returnModel = true)
	{
		if (isset($this->pagination))
			$this->pagination->prepare($this);
		$this->getQueryBuilder()->buildSelect($this, $sql, $args);
		// $index = 0;
		// echo preg_replace_callback('#\?#', function ($matches) use ($args, &$index) {
		// 	return "'" . $args[$index++] . "'";
		// }, $sql);
		$data = $this->getAdapter()->query($sql, $args, DbAdapter::MULTI, DbAdapter::FETCH_ASSOC, null);
		if ($returnModel && isset($this->model))
			return call_user_func([$this->model, 'newList'], $data, $this, Model::SOURCE_DB);
		return $data;
	}
	
	/**
	 * @return array|Model
	 */
	public function findOne(bool $returnModel = true)
	{
		$this->getQueryBuilder()->buildSelect($this->limit(1), $sql, $args);
		$data = $this->getAdapter()->query($sql, $args, DbAdapter::ONE, DbAdapter::FETCH_ASSOC, null);
		if ($returnModel && isset($this->model))
			return call_user_func([$this->model, 'newInstance'], $data, $this, Model::SOURCE_DB);
		return $data;
	}
	
	/**
	 * @return int
	 */
	public function count()
	{
		$this->select('COUNT(*)');
		$this->getQueryBuilder()->buildSelect($this, $sql, $args);
		if (!empty($this->group)) {
			/** @var Query $query */
			$query = new static();
			$query->from("({$sql})")->select('COUNT(*)');
			$query->getQueryBuilder()->buildSelect($query, $newSql);
			$sql = $newSql;
		}
		return $this->getAdapter()->query($sql, $args, DbAdapter::ONE, DbAdapter::FETCH_COLUMN, 0);
	}
	
	/**
	 * @param int|string $column
	 * @return array
	 */
	public function column($column)
	{
		if (isset($this->pagination))
			$this->pagination->prepare($this);
		$this->getQueryBuilder()->buildSelect($this, $sql, $args);
		return $this->getAdapter()->query($sql, $args, DbAdapter::MULTI, DbAdapter::FETCH_COLUMN, $column);
	}
	
	/**
	 * @param int|string $column
	 * @return string|int
	 */
	public function columnOne($column)
	{
		$this->getQueryBuilder()->buildSelect($this, $sql, $args);
		return $this->getAdapter()->query($sql, $args, DbAdapter::ONE, DbAdapter::FETCH_COLUMN, $column);
	}
	
	/**
	 * 设置查询的字段
	 *
	 * 每一次执行，都会清空已有查询字段，如果要追加查询，请使用addSelect
	 *
	 * ```php
	 * $query->select('id', 'name', 'title', ['test', 'ok', 'what']); // 支持多维数组格式
	 * ```
	 *
	 * @param mixed ...$args
	 * @return $this
	 */
	public function select(...$args)
	{
		$this->select = $args;
		return $this;
	}
	
	public function addSelect(...$args)
	{
		if (!empty($args)) {
			if (!is_array($this->select))
				$this->select = [$this->select];
			array_push($this->select, ...$args);
		}
		
		return $this;
	}
	
	public function clearSelect()
	{
		$this->select = [];
		return $this;
	}
	
	public function from(string $table)
	{
		$this->from = (string)$table;
		return $this;
	}
	
	public function clearFrom()
	{
		$this->from = false;
		return $this;
	}
	
	/**
	 * 联合表查询
	 *
	 * ```php
	 * $query->from('user');                                // tb1 => user
	 * $query->join('user_log.user_id', 'user.id');         // tb2 => user_log
	 * $query->join('user_log.user_id', 'tb1.id');          // 等同于上述
	 * $query->join('user_log', 'user_id', 'tb1.id');       // 等同于上述
	 * $query->join('user_log', 'user_id', 'tb1', 'id');    // 等同于上述
	 *
	 * $query->join(Sql::LEFT_JOIN, 'user_log.user_id', 'user.id'); // 指定连接的方式，第一个参数必须为int类型
	 * $query->join(Sql::LEFT_JOIN, 'user_log.user_id', 'user.id', [ 'tb1.id' => '1' ]); // 增加连接查询的条件
	 * ```
	 *
	 * @param array ...$args
	 * @return $this|Query
	 */
	public function join(...$args)
	{
		$this->join = [];
		return $this->addJoin(...$args);
	}
	
	public function addJoin(...$args)
	{
		if (!empty($args)) {
			if (is_array($args[0])) {
				foreach ($args as $arg) {
					$this->addJoin(...$arg);
				}
				return $this;
			}
			$this->join[] = $args;
		}
		return $this;
	}
	
	public function clearJoin()
	{
		$this->join = [];
		return $this;
	}
	
	/**
	 * Where查询
	 *
	 * ```php
	 * // id > 100
	 * $query->where('id', '>', 100);
	 * // id > 0 AND status in (1)
	 * $query->where(['id', '>', 0], ['status', 'in', 1]);
	 * // id > 0 OR status in (1)
	 * $query->where(['id', '>', 0], ['OR', 'status', 'in', 1]);
	 * // 复杂的查询结构
	 * $query->where([
	 *     [...]
	 *     'OR',
	 *     [
	 *         [...],
	 *         ['OR', ...],
	 *     ],
	 * ]);
	 * ```
	 *
	 * @param array ...$args
	 * @return $this|Query
	 */
	public function where(...$args)
	{
		$this->where = [];
		$this->addWhere(...$args);
		return $this;
	}
	
	public function addWhere(...$args)
	{
		if (empty($args))
			return $this;
		if (isset($args[0]) && is_array($args[0])) {
			$marry = null;
			foreach ($args as $arg) {
				if ($arg === 'AND' || $arg === 'OR') {
					$marry = $arg;
					continue;
				}
				if (is_array($arg)) {
					if ($marry !== null) {
						if ($arg[0] === 'AND' || $arg[0] === 'OR')
							$arg[0] = $marry;
						else
							array_unshift($arg, $marry);
						$marry = null;
					}
					$this->addWhere(...$arg);
				}
			}
			return $this;
		}
		$this->where[] = $args;
		return $this;
	}
	
	public function clearWhere()
	{
		$this->where = [];
		return $this;
	}
	
	private $inOperator = 'in';
	
	public function in(...$args)
	{
		if (empty($args))
			return $this;
		$marry = 'AND';
		if ($args[0] === 'AND' || $args[0] === 'OR') {
			$marry = array_shift($args);
		}
		if (is_array($args[0])) {
			// $query->in(['id' => 1, 'name' => 'jan']);
			// keyValue只处理第一个参数
			$data = [];
			foreach ($args[0] as $key => $value) {
				$data[] = [$key, $this->inOperator, $value];
			}
			return $this->addWhere($marry, $data);
		} else if (count($args) > 1) {
			// key, value, value
			return $this->addWhere($marry, array_shift($args), $this->inOperator, $args);
		}
		return $this;
	}
	
	public function notIn(...$args)
	{
		if (empty($args))
			return $this;
		$this->inOperator = '!in';
		$this->in(...$args);
		$this->inOperator = 'in';
		return $this;
	}

//	public function in($key, $values = null)
//	{
//		if (is_array($key)) {
//			foreach ($key as $k => $v) {
//				$this->in($k, $v);
//			}
//			return $this;
//		}
//		return $this->addWhere($key, 'in', ...(array)$values);
//	}
//
//	public function notIn($key, $values = null)
//	{
//		if (is_array($key)) {
//			foreach ($key as $k => $v) {
//				$this->notIn($k, $v);
//			}
//			return $this;
//		}
//		return $this->addWhere($key, '!in', ...(array)$values);
//	}
	
	public function limit(int $limit = -1)
	{
		if ($limit < 0)
			$limit = -1;
		$this->limit = $limit;
		return $this;
	}
	
	public function offset(int $value = -1)
	{
		if ($value < 0)
			$value = -1;
		$this->offset = $value;
		return $this;
	}
	
	/**
	 * Order查询
	 *
	 * ```php
	 * $query->order(['id', 1], 'name');        // id ASC, name
	 * $query->order('id ASC', 'status DESC');  // id ASC, status DESC
	 * $query->order([
	 *     ['id', 'status', 'created_at', 1],
	 *     ['updated_at', -1],
	 * ]);  // id,status,created_at ASC, updated_at DESC
	 * ```
	 *
	 * @param array ...$args
	 * @return $this|Query
	 */
	public function order(...$args)
	{
		$this->order = [];
		return $this->addOrder(...$args);
	}
	
	public function addOrder(...$args)
	{
		if (!empty($args)) {
			if (is_array($args[0])) {
				foreach ($args as $arg) {
					if (is_array($arg))
						$this->addOrder(...$arg);
					else if (is_string($arg))
						$this->order[] = $arg;
				}
				return $this;
			}
			$this->order[] = $args;
		}
		return $this;
	}
	
	public function clearOrder()
	{
		$this->order = [];
		return $this;
	}
	
	/**
	 * Group
	 *
	 * ```php
	 * $query->group('id');
	 * ```
	 *
	 * @param string|null $field
	 * @return $this
	 */
	public function group(string $field = null)
	{
		$this->group = empty($field) ? false : $field;
		return $this;
	}
	
	public function newPagination()
	{
		if (!isset($this->pagination)) {
			$this->pagination = new Pagination();
		}
		return $this->pagination;
	}
	
	public function getPagination()
	{
		return $this->pagination;
	}
	
	public function removePagination()
	{
		if (isset($this->pagination)) {
			$this->pagination = null;
			if ($this->limit > 0)
				$this->limit = 0;
			if ($this->offset > 0)
				$this->offset = 0;
		}
		return $this;
	}
	
	/**
	 * 分页查询
	 *
	 * ```php
	 * $query->paginate(10);
	 * $query->paginate([ 'size' => 10, 'current' => 1, 'field' => 'page' ]);
	 * ```
	 *
	 * @param int|array $options
	 * @return $this
	 */
	public function paginate($options, int $current = null)
	{
		$this->newPagination()->setOptions($options, $current);
		return $this;
	}
	
	public function sql()
	{
		$debug = $this->debug;
		$this->getQueryBuilder()->buildSelect($this->debug(true), $sql);
		$this->debug = $debug;
		return $sql;
	}
	
	
}