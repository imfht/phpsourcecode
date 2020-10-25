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
use Tang\Database\Sql\Schema\DDL;
use Tang\Database\Sql\Connections\Connection;

/**
 * Mysql
 * Class Mysql
 * @package Tang\Database\Sql\Schema\Grammar
 */
class Mysql extends Grammar
{
	protected $modifiers = array('Unsigned', 'Nullable', 'Default', 'Increment', 'After', 'Comment');
	protected $serials = array('bigInteger', 'integer', 'mediumInteger', 'smallInteger', 'tinyInteger');
	public function compileGetColumn($tableName)
	{
		return 'show columns from '.$tableName;
	}
	public function compileCreate(DDL $ddl, $command, Connection $connection)
	{
		$columns = implode(', ', $this->getColumns($ddl));
	
		$sql = 'create table '.$this->wrapTable($ddl->getTableName()).' ('.$columns.')';
		$sql = $this->compileCreateEncoding($sql, $connection);
		$engine = $ddl->getEngine();
		if ($engine)
		{
			$sql .= ' engine = '.$engine;
		}
		return $sql;
	}
	public function compileAdd(DDL $ddl,$command)
	{
		$table = $this->wrapTable($ddl->getTableName());
		$columns = $this->prefixArray('add', $this->getColumns($ddl));
		return 'alter table '.$table.' '.implode(', ', $columns);
	}
	public function compilePrimary(DDL $ddl,$command)
	{
		return $this->compileKey($ddl, $command, 'primary key');
	}
	public function compileUnique(DDL $ddl,$command)
	{
		return $this->compileKey($ddl, $command, 'unique index');
	}
	public function compileIndex(DDL $ddl,$command)
	{
		return $this->compileKey($ddl, $command, 'index');
	}
	protected function compileKey(DDL $ddl,$command,$type)
	{
		$columns = $this->columnize($command['columns']);
		$tableName = $this->wrapTable($ddl->getTableName());
		return  'alter table '.$tableName.' add '.$type.' '.$command['index'].'('.$columns.')'; 
	}
	public function compileDrop(DDL $ddl,$command)
	{
		return 'drop table '.$this->wrapTable($ddl->getTableName());
	}
    public function compileDropIfExists(DDL $ddl,$command)
	{
		return 'drop table if exists '.$this->wrapTable($ddl->getTableName());
	}
    public function compileDropColumn(DDL $ddl,$command)
	{
		$columns = $this->prefixArray('drop', $this->wrapArray($command['columns']));
		return 'alter table '.$this->wrapTable($ddl->getTableName()).' '.implode(', ', $columns);
	}
	public function compileDropPrimary(DDL $ddl,$command)
	{
		return 'alter table '.$this->wrapTable($ddl->getTableName()).' drop primary key';
	}
	public function compileDropUnique(DDL $ddl,$command)
	{
		return 'alter table '.$this->wrapTable($ddl->getTableName()).' drop index '.$command['index'];
	}
	public function compileDropIndex(DDL $ddl,$command)
	{
	 	return $this->compileDropUnique($ddl,$command);
	}
	public function compileDropForeign(DDL $ddl,$command)
	{
		return 'alter table '.$this->wrapTable($ddl->getTableName()).' drop foreign key '.$command['index'];
	}
	public function compileRename(DDL $ddl,$command)
	{
		return 'rename '.$this->wrapTable($ddl->getTableName()).' to '.$this->wrapTable($command['newTableName']);
	}
	protected function typeChar($column)
	{
		return 'char('.$column['length'].')';
	}
	protected function typeString($column)
	{
		return 'varchar('.$column['length'].')';
	}
	protected function typeText($column)
	{
		return 'text';
	}
	protected function typeMediumText($column)
	{
	return 'mediumtext';
	}
	protected function typeLongText($column)
	{
		return 'longtext';
	}
	protected function typeBigInteger($column)
	{
		return 'bigint';
	}
	protected function typeInteger($column)
	{
		return 'int';
	}
	protected function typeMediumInteger($column)
	{
		return 'mediumint';
	}
	protected function typeTinyInteger($column)
	{
		return 'tinyint';
	}
	protected function typeSmallInteger($column)
	{
		return 'smallint';
	}
	protected function typeFloat($column)
	{
		return 'float('.$column['total'].', '.$column['places'].')';
	}
	protected function typeDouble($column)
	{
		if (isset($column['total']) && isset($column['places']) && $column['total'] && $column['places'])
		{
		 	return 'double('.$column['total'].', '.$column['places'].')';
		}
		else
		{
			return 'double';
		}
	}
	protected function typeDecimal($column)
	{
		return 'decimal('.$column['total'].', '.$column['places'].')';
	}
	protected function typeBoolean($column)
	{
	 	return 'tinyint(1)';
	 }
	protected function typeEnum($column)
	{
		return 'enum(\''.implode('\', \'', $column['allowed']).'\')';
	}
	protected function typeDate($column)
	{
			return 'date';
	}
	protected function typeDateTime($column)
	{
		return 'datetime';
	}
	protected function typeTime($column)
	{
		return 'time';
	}
	protected function typeTimestamp($column)
	{
		if (!isset($column['nullable'])) return 'timestamp default 0';
		return 'timestamp';
	}
		
	protected function modifyUnsigned(DDL $ddl,$column)
	{
		if (isset($column['unsigned']) && $column['unsigned']) return ' unsigned';
	}
	protected function modifyNullable(DDL $ddl,$column)
	{
		return  isset($column['nullable']) && $column['nullable'] ? ' null' : ' not null';
	}
	protected function modifyDefault(DDL $ddl,$column)
	{
		if (isset($column['default']) && $column['default'])
		{
			return ' default '.$this->getDefaultValue($column['default']);
		}
	}
	protected function modifyIncrement(DDL $ddl,$column)
	{
		if (in_array($column['type'], $this->serials) && $column['autoIncrement'])
		{
			return ' auto_increment primary key';
		}
	}
	protected function modifyAfter(DDL $ddl,$column)
	{
		if(isset($column['after']) && $column['after'])
		{
			return ' after '.$this->wrap($column['after']);
		}
	}
	protected function modifyComment(DDL $ddl,$column)
	{
		if(isset($column['comment']) && $column['comment'])
		{
			return ' comment "'.$column['comment'].'"';
		}
	}
	protected function compileCreateEncoding($sql, Connection $connection)
	{
		$config = $connection->getConfig();
		if (isset($config['charset']) && $config['charset'])
		{
			$sql .= ' default character set '.$config['charset'];
		}
	
		if (isset($config['collate']) && $config['collate'])
		{
			$sql .= ' collate '.$config['collate'];
		}
	
		return $sql;
	}
	protected function wrapValue($value)
	{
		if ($value === '*') return $value;
		return '`'.str_replace('`', '``', $value).'`';
	}
}