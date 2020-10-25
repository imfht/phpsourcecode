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

/**
 * Class Pgsql
 * @package Tang\Database\Sql\Schema\Grammar
 */
class Pgsql extends Grammar
{
	protected $modifiers = array('Increment', 'Nullable', 'Default');
	protected $serials = array('bigInteger', 'integer');
	public function compileGetColumn($tableName)
	{
		$sql = <<<SQL
					SELECT
					      a.attname AS "columnName",
					      (SELECT 't'
					        FROM pg_index
					        WHERE c.oid = pg_index.indrelid
					        AND a.attnum = ANY (pg_index.indkey)
					        AND pg_index.indisprimary = 't'
					      ) IS NOT NULL AS "columnKey",
					      REGEXP_REPLACE(REGEXP_REPLACE(REGEXP_REPLACE((SELECT pg_attrdef.adsrc
					        FROM pg_attrdef
					        WHERE c.oid = pg_attrdef.adrelid
					        AND pg_attrdef.adnum=a.attnum
					      ),'::[a-z_ ]+',''),'''$',''),'^''','') AS default
					FROM pg_attribute a, pg_class c, pg_type t
					WHERE c.relname = '{$tableName}' 
					      AND a.attnum > 0
					      AND a.attrelid = c.oid
					      AND a.atttypid = t.oid
					ORDER BY a.attnum
SQL;
			return $sql;
	}
	
	public function compileCreate(DDL $ddl,$command)
	{
		$columns = implode(', ', $this->getColumns($ddl));
		return  'create table '.$this->wrapTable($ddl->getTableName()).' ('.$columns.')';
	}
	public function compileAdd(DDL $ddl,$command)
	{
		$table = $this->wrapTable($ddl->getTableName());
		$columns = $this->prefixArray('add column', $this->getColumns($ddl));
		return 'alter table '.$table.' '.implode(', ', $columns);
	}
	public function compilePrimary(DDL $ddl,$command)
	{
		$columns = $this->columnize($command['columns']);
		return 'alter table '.$this->wrapTable($ddl->getTableName())." add primary key ({$columns})";
	}
	public function compileUnique(DDL $ddl,$command)
	{
		$table = $this->wrapTable($ddl->getTableName());
		$columns = $this->columnize($command['columns']);
		return 'alter table '.$table.' add constraint '.$command['index'].' unique ('.$columns.')';
	}
	public function compileIndex(DDL $ddl,$command)
	{
		$columns = $this->columnize($command['columns']);
		return 'create index '.$command['index'].' on '.$this->wrapTable($ddl->getTableName()).' ('.$columns.')';
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
		$columns = $this->prefixArray('drop column', $this->wraps($command['columns']));
		$table = $this->wrapTable($ddl->getTableName());
		return 'alter table '.$table.' '.implode(', ', $columns);
	}
	public function compileDropPrimary(DDL $ddl,$command)
	{
		$table = $ddl->getTableName();
		return 'alter table '.$this->wrapTable($table).' drop constraint '.$table.'_pkey' ;
	}
	public function compileDropUnique(DDL $ddl,$command)
	{
		$table = $this->wrapTable($ddl->getTableName());
		return 'alter table '.$table.' drop constraint '.$command['index'];
	}
	public function compileDropIndex(DDL $ddl,$command)
	{
		return 'drop index '.$command['index'];
	}
	public function compileDropForeign(DDL $ddl,$command)
	{
		$table = $this->wrapTable($ddl->getTableName());
		return 'alter table '.$table.	' drop constraint '.$command['index'];
	}
	public function compileRename(DDL $ddl,$command)
	{
			$srcTableName = $this->wrapTable($ddl->getTableName());
			return 'alter table '.$srcTableName.' rename to '.$this->wrapTable($command['newTableName']);
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
	 	return 'text';
	}
	protected function typeLongText($column)
	{
		return 'text';
	}
	protected function typeInteger($column)
	{
		return $column['autoIncrement'] ? 'serial' : 'integer';
	}
	protected function typeBigInteger($column)
	{
		return $column['autoIncrement'] ? 'bigserial' : 'bigint';
	}
	protected function typeMediumInteger($column)
	{
		return 'integer';
	}
	protected function typeTinyInteger($column)
	{
		return 'smallint';
	}
	protected function typeSmallInteger($column)
	{
		return 'smallint';
	}
	protected function typeFloat($column)
	{
		return 'real';
	}
	protected function typeDouble($column)
	{
		return 'double precision';
	}
	protected function typeDecimal($column)
	{
		return 'decimal('.$column['total'].', '.$column['places'].')';
	}
	protected function typeBoolean($column)
	{
		return 'boolean';
	}
	protected function typeEnum($column)
	{
		$allowed = array_map(function($a) { return '\''.$a.'\''; }, $column['allowed']);
		return 'varchar(255) check ("'.$column['name'].'" in ('.implode(', ', $allowed).'))';
	}
	protected function typeDate($column)
	{
		return 'date';
	}
	protected function typeDateTime($column)
	{
		return 'timestamp';
	}
	protected function typeTime($column)
	{
		return 'time';
	}
	protected function typeTimestamp($column)
	{
		return 'timestamp';
	}
	protected function typeBinary($column)
	{
		return 'bytea';
	}
	protected function modifyNullable(DDL $ddl,$column)
	{
		return $column['nullable'] ? ' null' : ' not null';
	}
	protected function modifyDefault(DDL $ddl,$column)
	{
		if (isset($column['default']) &&$column['default'])
		{
			return ' default '.$this->getDefaultValue($column->default);
		}
	}
	protected function modifyIncrement(DDL $ddl,$column)
	{
		if (in_array($column['type'], $this->serials) && $column['autoIncrement'])
		{
			return ' primary key';
		}
	}
}