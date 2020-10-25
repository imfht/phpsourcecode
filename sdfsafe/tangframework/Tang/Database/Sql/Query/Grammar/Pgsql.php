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
 * Pgsql查询解析器
 * Class Pgsql
 * @package Tang\Database\Sql\Query\Grammar
 */
class Pgsql extends  Grammar
{
	protected static $lock = array(
			1 => 'for update',
			2 => 'for share'
	);
	protected function compileLock(Builder $query, $value)
	{
		return isset(static::$lock[$value]) && static::$lock[$value] ? static::$lock[$value] :'';
	}
	public function compileUpdate(Builder $query, $values)
	{
		$table = $this->wrapTable($query->getBindings('tableName'));
		$columns = $this->compileUpdateColumns($values);
		$from = $this->compileUpdateFrom($query);
		$where = $this->compileUpdateWheres($query);
		return trim('update '.$table.' set '.$columns.$from.' '.$where);
	}
	public function compileInsertGetId(Builder $query, $values, $sequence)
	{
		if (is_null($sequence)) $sequence = 'id';
		return $this->compileInsert($query, $values).' returning '.$this->wrap($sequence);
	}
	public function compileTruncate(Builder $query)
	{
		return array('truncate '.$this->wrapTable($query->getBindings('tableName')).' restart identity' => array());
	}
	protected function compileUpdateColumns($values)
	{
		$columns = array();
		foreach ($values as $key => $value)
		{
			$columns[] = $this->wrap($key).' = ?';
		}
		return implode(', ', $columns);
	}
	protected function compileUpdateFrom(Builder $query)
	{
		$joins = $query->getBindings('joins');
		if (!$joins) return '';
		$froms = array();
		foreach ($joins as $join)
		{
			$froms[] = $this->wrapTable($join['tableName']);
		}
	
		if (count($froms) > 0) return ' from '.implode(', ', $froms);
	}
	protected function compileUpdateWheres(Builder $query)
	{
		$baseWhere = $this->compileWheres($query);
		$joins = $query->getBindings('joins');
		if (!$joins) return $baseWhere;
		$joinWhere = $this->compileUpdateJoinWheres($query);
	
		if (trim($baseWhere) == '')
		{
			return 'where '.$this->removeLeadingBoolean($joinWhere);
		}
		else
		{
			return $baseWhere.' '.$joinWhere;
		}
	}
	protected function compileUpdateJoinWheres(Builder $query)
	{
		$joinWheres = array();
		$joins = $query->getBindings('joins');
		foreach ($joins as $join)
		{
			foreach ($join->clauses as $clause)
			{
				$joinWheres[] = $this->compileJoinConstraint($clause);
			}
		}
		return implode(' ', $joinWheres);
	}
}