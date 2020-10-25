<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Database\Sql\Query;
use Tang\Database\Sql\Query\Grammar\Grammar;
use Tang\Database\Sql\Connections\Connection;
use Closure;
use Tang\Pagination\PaginationService;

/**
 * SQL语句查询构建类
 * @package Tang\Database\Sql\Query
 */
class Builder
 {
    /**
     * 数据库连接
     * @var Connection
     */
    protected $connection = null;
    /**
     * 解释器
     * @var Grammar
     */
    protected $grammar = null;
    /**
     * 绑定值
     * @var Array
     */
    protected $bindings = array(
    		'select' => [],
    		'join'   => [],
    		'where'  => [],
    		'having' => [],
    		'order'  => [],
    );
	/**
	 * 聚集数组
	 * @var array
	 */
	public $aggregate = array();
    /**
     * 查询的字段
     * @var array
     */
    protected $columns = array('*');
    /**
     * 查询返回不同的结果
     * @var bool
     */
    protected $distinct = false;
    /**
     * 表名称
     * @var string
     */
    protected $tableName = '';
    /**
	 * join数组信息
     * @var array
     */
    protected $joins = array();
    /**
     * @var array
     */
    protected $wheres = array();
    /**
     * @var array
     */
    protected $groups = array();
	/**
	 * @var array
	 */
    protected $havings = array();
	/**
	 * @var array
	 */
    protected $orders = array();
	/**
	 * @var int
	 */
    protected $limit = 0;
	/**
	 * @var int
	 */
	protected $offset = 0;
	/**
     * unions
	 * @var array
	 */
    protected $unions = array();
    /**
     * 是否锁表
     * @var bool
     */
	protected $lock = false;
	public function __construct(Connection $connection,Grammar $grammar)
	{
		$this->connection = $connection;
		$this->grammar = $grammar;
	}

	/**
	 * 清除查询条件
	 * @return $this
	 */
	public function clean()
    {
        $this->unions = $this->orders = $this->havings = $this->groups = $this->wheres = $this->joins = $this->aggregate = array();
        $this->limit = $this->offset = 0;
        $this->columns = array('*');
        $this->lock = false;
        $this->bindings = array(
            'select' => [],
            'join'   => [],
            'where'  => [],
            'having' => [],
            'order'  => [],
        );
        return $this;
    }

	/**
	 * 设置表名
	 * @param $tableName
	 * @return $this
	 */
	public function setTable($tableName)
	{
		$this->tableName = $tableName;
		return $this;
	}

    /**
     * 获取属性
     * @param $name
     * @param null $default
     * @return null
     */
    public function getProperty($name,$default=null)
	{
		return property_exists($this, $name) ? $this->$name:$default;
	}

	/**
	 * 获取绑定的参数
	 * @return array
	 */
	public function getBindings()
	{
		$return = array();
		array_walk_recursive($this->bindings, function($x) use (&$return) { $return[] = $x; });
		return $return;
	}

	/**
	 * update
	 * @param array $data
	 * @return int
	 */
	public function update(array $data)
    {
        $bindings = array_values(array_merge($data, $this->getBindings()));
        $sql = $this->grammar->compileUpdate($this,$data);
        return $this->connection->statement($sql,$bindings);
    }

	/**
	 * insert
	 * @param array $datas
	 * @return int
	 */
	public function insert(array $datas)
	{
		if (!is_array(reset($datas)))
		{
			$datas = array($datas);
		}else
		{
			foreach ($datas as $key => $data)
			{
				ksort($data); $datas[$key] = $data;
			}
		}
		$bindings = array();
		
		foreach ($datas as $record)
		{
			foreach ($record as $value)
			{
				$bindings[] = $value;
			}
		}
		$sql = $this->grammar->compileInsert($this,$datas);
		return $this->connection->statement($sql, $bindings);
	}

	/**
	 * 删除
	 * @return int
	 */
	public function delete()
	{
		$sql = $this->grammar->compileDelete($this);
		return $this->connection->statement($sql, $this->getBindings());
	}
	/**
	 * 查询
	 * @param array $columns
     * @param mixed $index 索引
	 * @return Array
	 */
	public function get($columns = array(),$index = '')
	{
        if($columns)
        {
            $this->columns = $columns;
        }
		return $this->connection->select($this->toSql(), $this->getBindings(),$index);
	}

	/**
	 * 根据$column字段$value值进行查询
	 * @param $column
	 * @param $value
	 * @return mixed
	 */
	public function find($column,$value)
	{
		return $this->where($column, '=', $value)->first();
	}

	/**
	 * 查询第一条
	 * @param array $columns
	 * @return mixed|null
	 */
	public function first($columns = array('*'))
	{
		$results = $this->take(1)->get($columns);
		return count($results) > 0 ? reset($results) : null;
	}

	/**
	 * 转换成SQL语句
	 * @return string
	 */
	public function toSql()
	{
		return $this->grammar->compileSelect($this);
	}

    /**
     * 设置查询字段
     * <code>
     * $query->select();//查询所有
     * $query->select('id','name','first');//查询id name first字段
     * $query->select(array('id','name','first'));//查询id name first字段
     * </code>
     * @param  array $columns
     * @return $this
     */
    public function select($columns = array('*'))
    {
        $this->columns = is_array($columns) ? $columns : func_get_args();
		return $this;
    }


    /**
     * 增加查询字段
     * @param mixed $column
     * @return $this
     */
    public function appendSelect($column =array())
    {
        $column = is_array($column) ? $column : func_get_args();
        if($column)
        {
            $this->columns = array_merge($this->columns,$column);
        }

		return $this;
    }
    /**
     * 是否返回唯一不同的值。
     * @param  bool $distinct
     * @return $this
     */
    public function setDistinct($distinct)
    {
        $this->distinct = $distinct;
        return $this;
    }

    /**
     * JOIN查询
     * @param string $tableName 表名
     * @param mixed $fromColumn 表字段或者回调方法
     * @param null $operator 操作符
     * @param null $joinColumnOrValue 连接字段或者值
     * @param string $type inner left right类型 默认为inner
     * @param bool $isWhere 是否包含where
     * @return $this
     */
    public function join($tableName, $fromColumn, $operator = null, $joinColumnOrValue = null, $type = 'inner',$isWhere = false)
    {
    	$join = new Join($this, $type, $tableName);
    	if ($fromColumn instanceof \Closure)
    	{
    		$this->joins[] = $join;
    		call_user_func($fromColumn, $join);
    	}else
		{
			$this->joins[] = $join->on(
				$fromColumn, $operator, $joinColumnOrValue, 'and', $isWhere
			);
		}
       return $this;
    }

	/**
	 * join where
	 * @param $tableName
	 * @param $fromColumn
	 * @param $operator
	 * @param $value
	 * @param string $type
	 * @return $this
	 */
	public function joinWhere($tableName, $fromColumn, $operator, $value, $type = 'inner')
    {
    	return $this->join($tableName, $fromColumn, $operator, $value, $type, true);
    }

	/**
	 * leftJoin
	 * @param $tableName
	 * @param $fromColumn
	 * @param null $operator
	 * @param null $joinColumn
	 * @return $this
	 */
	public function leftJoin($tableName, $fromColumn, $operator = null, $joinColumn = null)
    {
    	return $this->join($tableName, $fromColumn, $operator, $joinColumn, 'left');
    }

	/**
	 * leftJoin where
	 * @param $tableName
	 * @param $fromColumn
	 * @param $operator
	 * @param $value
	 * @return $this
	 */
	public function leftJoinWhere($tableName, $fromColumn, $operator, $value)
    {
    	return $this->joinWhere($tableName, $fromColumn, $operator, $value, 'left');
    }

	/**
	 * right Join
	 * @param $tableName
	 * @param $fromColumn
	 * @param null $operator
	 * @param null $joinColumn
	 * @return $this
	 */
	public function rightJoin($tableName, $fromColumn, $operator = null, $joinColumn = null)
    {
    	return $this->join($tableName, $fromColumn, $operator, $joinColumn, 'right');
    }

	/**
	 * right Join Where
	 * @param $tableName
	 * @param $fromColumn
	 * @param $operator
	 * @param $value
	 * @return $this
	 */
	public function rightJoinWhere($tableName, $fromColumn, $operator, $value)
    {
    	return $this->joinWhere($tableName, $fromColumn, $operator, $value, 'right');
    }

	/**
	 * union联合查询
	 * @param Builder $query 另一个查询构造器
	 * @param bool $all 是否union all
	 * @return Builder
	 */
	public function union(Builder $query,$all = false)
	{
		$this->unions[] = compact('query', 'all');
		return $this->mergeBindings($query);
	}

	/**
	 * union all联合查询
	 * @param Builder $query
	 * @return Builder
	 */
	public function unionAll(Builder $query)
	{
		return $this->union($query,true);
	}

	/**
	 * where操作
	 * @param string $column <p>字段</p>
	 * @param null $operator 操作符 例如 = + - < like
	 * @param null $value 值
	 * @param string $connector 操作类型 例如and or
	 * @return $this
	 */
	public function where($column,$operator = null,$value = null,$connector = 'and')
	{
		if($column instanceof \Closure)
		{
			return $this->whereNested($column,$connector);
		}
		$type = 'Basic';
		$this->wheres[] = compact ( 'type', 'column', 'operator', 'value', 'connector' );
		return $this->addBinding('where', $value);
	}

	/**
	 * where sql操作
	 * @param string $sql sql语句
	 * @param array $bindings 绑定的参数
	 * @param string $connector 操作类型 例如and or
	 * @return $this
	 */
	public function whereSql($sql,$bindings = array(), $connector = 'and')
	{
		$type = 'sql';
		$this->wheres[] = compact('type', 'sql', 'connector');
		return $this->addBinding('where',$bindings);
	}

	/**
	 * where between 操作
	 * @param string $column 字段
	 * @param mixed $min 最小值
	 * @param mixed $max 最大值
	 * @param string $connector 操作类型 例如and or
	 * @param bool $isNot 是否not between
	 * @return mixed
	 */
	public function whereBetween($column,$min,$max, $connector = 'and', $isNot = false)
	{
		$type = 'between';
		$this->wheres[] = compact('column', 'type', 'connector', 'isNot');
		return $this->addBinding('where',$min)->addBinding('where', $max);
	}

	/**
	 * or where between
	 * @param $column
	 * @param $min
	 * @param $max
	 * @return mixed
	 */
	public function orWhereBetween($column, $min,$max)
	{
		return $this->whereBetween($column, $min, $max,'or');
	}

	/**
	 * where Not Between操作
	 * @param $column
	 * @param $min
	 * @param $max
	 * @return mixed
	 */
	public function whereNotBetween($column, $min,$max)
	{
		return $this->whereBetween($column,$min,$max,'and',true);
	}

	/**
	 * or Not Between操作
	 * @param $column
	 * @param $min
	 * @param $max
	 * @return mixed
	 */
	public function orWhereNotBetween($column, $min,$max)
	{
		return $this->whereBetween($column, $min, $max,'or',true);
	}

	/**
	 * where嵌套查询
	 * @param callable $callback 回调函数，该函数将接受一个Builder对象值
	 * @param string $connector 是and还是or操作
	 * @return $this
	 */
	public function whereNested(Closure $callback, $connector = 'and')
	{
			$query = $this->createNewQuery();
			$query->setTable($this->tableName);
			call_user_func($callback, $query);
			if($query->getProperty('wheres'))
			{
				$type = 'Nested';
				$this->wheres[] = compact('type', 'query', 'connector');
				$this->mergeBindings($query);
			}
			return $this;
	}

    /**
     * 增加一个子查询
     * @param string $column 字段名称
     * @param string $operator 操作符号
     * @param callable $callback 查询条件闭包，该函数将接受一个Builder对象值
     * @param string $connector nd还是or
     * @return $this
     */
    protected function whereSub($column, $operator, Closure $callback, $connector)
	{
		$type = 'sub';
		$query = $this->createNewQuery();
		call_user_func($callback,$query);
		$this->wheres[] = compact('type', 'column', 'operator', 'query', 'connector');
		return $this->mergeBindings($query);
	}

	/**
	 * where exists
	 * @param callable $callback 该函数将接受一个Builder对象值
	 * @param string $connector and还是or
	 * @param bool $isNot 是否为not exists
	 * @return Builder
	 */
	public function whereExists(Closure $callback, $connector = 'and', $isNot = false)
	{
		$type = 'Exists';
		$query = $this->createNewQuery();
		call_user_func($callback, $query);
		$this->wheres[] = compact('type', 'operator', 'query', 'connector','isNot');
		return $this->mergeBindings($query);
	}

	/**
	 * where not exists
	 * @param callable $callback
	 * @param string $connector
	 * @return Builder
	 */
	public function whereNotExists(Closure $callback, $connector = 'and')
	{
		return $this->whereExists($callback, $connector, true);
	}

	/**
	 * or where exists
	 * @param callable $callback
	 * @param bool $not
	 * @return Builder
	 */
	public function orWhereExists(Closure $callback, $not = false)
	{
		return $this->whereExists($callback, 'or', $not);
	}

	/**
	 * or where not exists
	 * @param callable $callback
	 * @return Builder
	 */
	public function orWhereNotExists(Closure $callback)
	{
		return $this->whereExists($callback, 'or', true);
	}

	/**
	 * where in查询
	 * @param string $column 查询的字段
	 * @param mixed $values 查询的值 例如array(1,2,3); 如果为闭包的话，则为子查询查阅whereInSub
	 * @param string $connector and还是or
	 * @param bool $isNot 是否为not in
	 * @return $this|Builder
	 */
	public function whereIn($column,$values,$connector = 'and', $isNot = false)
	{
		if ($values instanceof Closure)
		{
			return $this->whereInSub($column, $values,$connector, $isNot);
		}
		$type = 'In';
		$this->wheres[] = compact('type', 'column', 'values', 'connector','isNot');
		return $this->addBinding('where',$values);
	}

	/**
	 * where not in操作
	 * 参数同whereIn
	 * @param $column
	 * @param $values
	 * @param string $connector
	 * @return $this|Builder
	 */
	public function whereNotIn($column, $values, $connector = 'and')
	{
		return $this->whereIn($column, $values, $connector, true);
	}

	/**
	 * or where in
	 * @param $column
	 * @param $values
	 * @return $this|Builder
	 */
	public function orWhereIn($column, $values)
	{
		return $this->whereIn($column, $values);
	}

	/**
	 * or where not in
	 * @param $column
	 * @param $values
	 * @return $this|Builder
	 */
	public function orWhereNotIn($column, $values)
	{
		return $this->whereIn($column, $values, 'or',true);
	}

	/**
	 * where in 子查询
	 * 将组建类似于：where $column in (select i from ...)
	 * @param string $column 字段名
	 * @param callable $callback 子语句的回调，该回调将接受Builder对象
	 * @param string $connector  and还是or
	 * @param boolean $isNot 是否为not in
	 * @return Builder
	 */
	protected function whereInSub($column, \Closure $callback, $connector,$isNot)
	{
		$type = 'InSub';
		$query = $this->createNewQuery();
		call_user_func($callback, $query);
		$this->wheres[] = compact('type', 'column', 'query', 'connector','isNot');
		return $this->mergeBindings($query);
	}

	/**
	 * where null操作
	 * @param string $column 字段名
	 * @param string $connector and还是or
	 * @param bool $isNot 是否为not null
	 * @return $this
	 */
	public function whereNull($column,$connector = 'and', $isNot = false)
	{
		$type = 'WhereNull';
		$this->wheres[] = compact('type', 'connector', 'column','isNot');
		return $this;
	}

	/**
	 * not null操作
	 * @param string $column 字段名
	 * @param string $connector and还是or
	 * @return $this
	 */
	public function whereNotNull($column, $connector = 'and')
	{
		return $this->whereNull($column,$connector,true);
	}

	/**
	 * or where null
	 * @param string $column
	 * @return $this
	 */
	public function orWhereNull($column)
	{
		return $this->whereNull($column,'or');
	}

	/**
	 * or where not null
	 * @param string $column
	 * @return $this
	 */
	public function orWhereNotNull($column)
	{
		return $this->whereNull($column,'or',true);
	}

	/**
	 * group by 
	 * @param mixed $columns 可为字符串，可为数组
     * @return $this
	 */
	public function groupBy($columns)
	{
		if(!is_array($columns))
		{
			$columns = array($columns);
		}
		$this->groups = array_merge($this->groups,$columns);
        return $this;
	}

	/**
	 * count聚集函数
	 * @param string $columns 字段
	 * @return int
	 */
	public function count($columns = '*')
	{
		return $this->aggregate('count',$columns);
	}

	/**
	 * min聚集函数
	 * @param string $column 字段
	 * @return int
	 */
	public function min($column)
	{
		return $this->aggregate('min',$column);
	}

	/**
	 * max聚集函数
	 * @param string $column 字段
	 * @return int
	 */
	public function max($column)
	{
		return $this->aggregate('max',$column);
	}

	/**
	 * sum聚集函数
	 * @param string $column 字段
	 * @return int
	 */
	public function sum($column)
	{
        return $this->aggregate('sum', $column);
	}

	/**
	 * avg 聚集函数
	 * @param string $column 字段
	 * @return int
	 */
	public function avg($column)
	{
		return $this->aggregate('avg',$column);
	}

	/**
	 * 聚集函数处理，
	 * @param string $name 函数名称
	 * @param mixed $columns 字段
	 * @return int
	 */
	public function aggregate($name,$columns= array('*'))
	{
		if (!is_array($columns))
		{
			$columns = array($columns);
		}
		$this->aggregate = compact('name', 'columns');
		$tempColumns = $this->columns;
		$results = $this->get($columns);
		$this->aggregate = null;
		$this->columns = $tempColumns;
		if (isset($results[0]))
		{
			$result = array_change_key_case((array) $results[0]);
			return $result['aggregate'];
		}
		return 0;
	}

	/**
	 * having操作
	 * @param $column 字段名
	 * @param $operator 操作符
	 * @param $value 值
	 * @param string $connector and 还是 or
	 * @return $this
	 */
	public function having($column, $operator, $value,$connector='and')
	{
		$type = 'Basic';
		$this->havings[] = compact('type','column','operator','value','connector');
		return $this->addBinding('having',$value);
	}

	/**
	 * or having
	 * @param $column
	 * @param $operator
	 * @param null $value
	 * @return $this
	 */
	public function orHaving($column, $operator, $value = null)
	{
		return $this->having($column, $operator, $value, 'or');
	}

    /**
     * 增加having sql
     * @param string $sql sql语句
     * @param array $bindings 绑定参数
     * @param string $connector 连接符
     * @return $this
     */
    public function havingSql($sql,array $bindings = array(), $connector = 'and')
	{
		$type = 'sql';
		$this->havings[] = compact('type','sql','connector');
		return $this->addBinding('having',$bindings);
	}

    /**
     * 增加having or sql
     * @param string $sql
     * @param array $bindings
     * @return $this
     */
    public function orHavingSql($sql,array $bindings = array())
	{
		return $this->havingSql($sql,$bindings);
	}

	/**
	 * order by
	 * @param $column 字段
	 * @param string $direction 排序方式 asc desc
	 * @return $this
	 */
	public function orderBy($column, $direction = 'asc')
	{
		$direction = strtolower($direction) == 'asc' ? 'asc' : 'desc';
		$this->orders[] = compact('column', 'direction');
		return $this;
	}

	/**
	 * 设置偏移量
	 * @param $value
	 * @return $this
	 */
	public function offset($value)
	{
		$this->offset = max(0, $value);
		return $this;
	}

	/**
	 * 设置数量
	 * @param $value
	 * @return $this
	 */
	public function limit($value)
	{
		$value = (int)$value;
		if ($value > 0) $this->limit = $value;
		return $this;
	}

	/**
	 * limit别名
	 * @param $value
	 * @return $this
	 */
	public function take($value)
    {
        return $this->limit($value);
    }

	/**
	 * 锁表
	 * @param int $lock
	 * @return $this
	 */
	public function lock($lock = 1)
	{
		$this->lock = $lock;
		return $this;
	}

	/**
	 * shared lock
	 * @return $this
	 */
	public function sharedLock()
	{
		return $this->lock(2);
	}

	/**
	 * for update lock
	 * @return $this
	 */
	public function forUpdateLock()
	{
		return $this->lock(1);
	}

	/**
	 * 解锁
	 * @return $this
	 */
	public function unlock()
	{
		return $this->lock(0);
	}

    /**
     * 分页设置
     * @param  int  $page
     * @param  int  $listRows
     * @return $this
     */
    public function forPage($page,$listRows = 20)
    {
        return $this->offset(($page - 1) * $listRows)->take($listRows);
    }

    /**
     * 获取分页信息
     * 返回数组
     * result 结果信息  pages 分页结果 maxPage 最大页数  count总数 page 当前页数
     * @param $page
     * @param int $listRows
     * @return array
     */
    public function getPagination($page,$listRows=20)
    {
        $orders = $this->orders;
        $limit = $this->limit;
        $offset = $this->offset;
        $count = $this->count();
        $this->orders = $orders;
        $this->limit = $limit;
        $this->offset = $offset;
        $page = (int)$page;
        if($page < 1)
        {
            $page = 1;
        }
        $pageService = PaginationService::getService();
        $pages = $pageService->getPages($page,$count,$listRows);
        $result = $this->forPage($page,$listRows)->get();
        return array('result' => $result,'pages'=>$pages,'maxPage'=>$pageService->getMaxPage(),'count'=>$count,'currentPage' =>$pageService->getNowPage());
    }

	/**
	 * 创建一个新的Builder对象
	 * @return Builder
	 */
	public function createNewQuery()
	{
		return new Builder($this->connection, $this->grammar);
	}

	/**
	 * 合并绑定的值
	 * @param Builder $query
	 * @return $this
	 */
	protected function mergeBindings(Builder $query)
	{
		$this->bindings = array_merge_recursive($this->bindings, $query->getProperty('bindings'));
		return $this;
	}

	/**
	 * 增加绑定
	 * @param $type
	 * @param $value
	 * @return $this
	 */
	public function addBinding($type,$value)
	{
		if(!isset($this->bindings[$type]))
		{
			return;
		}
		if (is_array($value))
		{
			$this->bindings[$type] = array_values(array_merge($this->bindings[$type], $value));
		}
		else
		{
			$this->bindings[$type][] = $value;
		}
		return $this;
	}
}