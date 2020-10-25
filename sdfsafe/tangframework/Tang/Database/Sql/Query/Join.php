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
/**
 * 连接类
 * Class Join
 * @package Tang\Database\Sql\Query
 */
class Join
{
    /**
     * 查询构建对象
     * @var Builder
     */
    protected $query;
    /**
     * 类型
     * @var string
     */
    protected $type;
    /**
     * 连接表名
     * @var string
     */
    protected $tableName;
    /**
     * join条件
     * @var array
     */
    protected $clauses = array();

    /**
     * @param Builder $query
     * @param $type
     * @param $tableName
     */
    public function __construct(Builder $query,$type,$tableName)
	{
		$this->query = $query;
		$this->type = $type;
		$this->tableName = $tableName;
	}

    /**
     * 获取查询构建对象
     * @return Builder
     */
    public function getQuery()
	{
		return $this->query;
	}

    /**
     * 获取连接类型
     * @return string
     */
    public function getType()
	{
		return $this->type;
	}

    /**
     * 获取表名
     * @return string
     */
    public function getTableName()
	{
		return $this->tableName;
	}
	public function getClauses()
	{
		return $this->clauses;
	}

    /**
     * on操作
     * @param $fromColumn
     * @param $operator
     * @param $joinColumnOrValue
     * @param string $connector
     * @param bool $isWhere
     * @return $this
     */
    public function on($fromColumn, $operator, $joinColumnOrValue, $connector = 'and', $isWhere = false)
	{
		$this->clauses[] = compact('fromColumn', 'operator', 'joinColumnOrValue', 'connector', 'isWhere');
		if ($isWhere) $this->query->addBinding('join',$joinColumnOrValue);
		return $this;
	}

    /**
     * or on
     * @param $fromColumn
     * @param $operator
     * @param $joinColumnOrValue
     * @return $this
     */
    public function orOn($fromColumn, $operator, $joinColumnOrValue)
	{
		return $this->on($fromColumn, $operator, $joinColumnOrValue, 'or');
	}

    /**
     * where 操作
     * @param $fromColumn
     * @param $operator
     * @param $value
     * @param string $connector
     * @return $this
     */
    public function where($fromColumn, $operator, $value, $connector = 'and')
	{
		return $this->on($fromColumn, $operator, $value, $connector, true);
	}

    /**
     * or where操作
     * @param $fromColumn
     * @param $operator
     * @param $value
     * @return $this
     */
    public function orWhere($fromColumn, $operator, $value)
	{
		return $this->on($fromColumn, $operator, $value, 'or', true);
	}
}