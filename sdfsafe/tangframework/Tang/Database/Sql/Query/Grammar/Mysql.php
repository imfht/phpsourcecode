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
namespace Tang\Database\Sql\Query\Grammar;
use Tang\Database\Sql\Query\Builder;

/**
 * Mysql查询解析器
 * Class Mysql
 * @package Tang\Database\Sql\Query\Grammar
 */
class Mysql extends Grammar
{
	protected $selectComponents = array(
			'aggregate',
			'columns',
			'tableName',
			'joins',
			'wheres',
			'groups',
			'havings',
			'orders',
			'limit',
			'offset',
			'lock',
	);
	protected static $lock = array(
		1 => 'for update',
		2 => 'lock in share mode'
	);
	public function compileSelect(Builder $query)
	{
		$sql = parent::compileSelect($query);
		if ($query->getProperty('unions'))
		{
			$sql = '('.$sql.') '.$this->compileUnions($query);
		}
		return $sql;
	}
	public function compileUpdate(Builder $query, $values)
	{
		$sql = parent::compileUpdate($query, $values);
		$orders = $query->getProperty('orders');
		if ($orders)
		{
			$sql .= ' '.$this->compileOrders($query,$orders);
		}
		$limit = $query->getProperty('limit');
		if ($limit)
		{
			$sql .= ' '.$this->compileLimit($query, $limit);
		}
		return rtrim($sql);
	}
	protected function compileUnion(array $union)
	{
		$joiner = $union['all'] ? ' union all ' : ' union ';
		return $joiner.'('.$union['query']->toSql().')';
	}
	protected function compileLock(Builder $query, $value)
	{
		return isset(static::$lock[$value]) && static::$lock[$value] ? static::$lock[$value] :'';
	}
	protected function wrapValue($value)
	{
		if ($value === '*') return $value;
		return '`'.str_replace('`', '``', $value).'`';
	}
}