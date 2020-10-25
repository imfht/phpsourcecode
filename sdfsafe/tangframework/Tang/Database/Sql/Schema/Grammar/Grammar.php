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
namespace Tang\Database\Sql\Schema\Grammar;
use Tang\Database\Sql\Grammar as BaseGrammar;
use Tang\Database\Sql\Schema\DDL;

/**
 * 表结构解析器
 * Class Grammar
 * @package Tang\Database\Sql\Schema\Grammar
 */
abstract class Grammar extends BaseGrammar
{
	protected $modifiers = array();
	public abstract function compileGetColumn($tableName);
	public function prefixArray($prefix, array $values)
	{
		return array_map(function($value) use ($prefix)
		{
			return $prefix.' '.$value;
		}, $values);
	}
	public function compileForeign(DDL $ddl, $command)
	{
		$tableName = $this->wrapTable($ddl->getTableName());
		$on = $this->wrapTable($command['onTable']);
		$column = $this->wrap($command['column']);
		$onColumn = $this->wrap($command['onColumn']);;
		$sql = 'alter table '.$tableName.' add constraint  '.$command['index'];
		$sql .= ' foreign key ('.$column.') references '.$on.'('.$onColumn.')';
		if (!is_null($command['onDelete']))
		{
			$sql .= ' on delete '.$command['onDelete'];
		}
		if (!is_null($command['onUpdate']))
		{
			$sql .= ' on update '.$command['onUpdate'];
		}
		return $sql;
	}
	protected function getColumns(DDL $ddl)
	{
		$columns = array();
		foreach ($ddl->getColumns() as $column)
		{
			$sql = $this->wrap($column['name']).' '.$this->getType($column);
			$columns[] = $sql.$this->addModifiers($ddl, $column);
		}
		return $columns;
	}
	protected function getType($column)
	{
		return $this->{'type'.ucfirst($column['type'])}($column);
	}
	protected function addModifiers(DDL $ddl,$column)
	{
		$sql = '';
		foreach ($this->modifiers as $modifier)
		{
			if (method_exists($this, $method = "modify{$modifier}"))
			{
				$sql .= $this->{$method}($ddl, $column);
			}
		}
		return $sql;
	}
}