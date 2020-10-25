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


class QueryTables
{
	
	protected static $tableRegex = '#(\(.*\)|(?:([a-z0-9_-]+)\.|)([a-z0-9_-]+))(?:[\s\t]+(?:as[\s\t]+)?([a-z0-9_-]+))?#i';
	
	private $tables = [];
	
	private $tableCounter = 1;
	
	private $tableMappings = [];
	
	private $tableAsPrefix = 'tb';
	
	private $useAlias = false;
	
	private function addSubKey(string $table, string $subKey = '')
	{
		if (!empty($subKey))
			$table .= ':' . $subKey;
		return $table;
	}
	
	public function parseTable(string $table)
	{
		$key = trim(strtolower($table));
		if (empty($key))
			return false;
		global $KE;
		$tables = &$KE['mysql_tables'];
		if (!isset($tables[$key])) {
			if (preg_match(self::$tableRegex, $table, $matches)) {
				// 1 => db.table, (SELECT * FROM TABLE)
				// 2 => db.table => db
				// 3 => db.table => table
				// 4 => as xxx => xxx
				if (empty($matches[2]) && empty($matches[3])) {
					// (SELECT * FROM TABLE)
					$data = [
						'name'    => $matches[1],
						'as'      => empty($matches[4]) ? null : $matches[4],
						'db'      => null,
						'isQuery' => true,
					];
				} else {
					$data = [
						'name'    => $matches[3], // 不再强制将 table 转为小写
						'as'      => empty($matches[4]) ? null : $matches[4],
						'db'      => empty($matches[2]) ? null : $matches[2],
						'isQuery' => false,
					];
				}
				$tables[$key] = $data;
			} else {
				$tables[$key] = false;
			}
		}
		return $tables[$key];
	}
	
	public function setTableAsPrefix(string $prefix)
	{
		if (!empty($prefix = trim($prefix)))
			$this->tableAsPrefix = $prefix;
		return $this;
	}
	
	/**
	 * 添加表，如果是主表，索引默认为1
	 *
	 * @param string $table
	 * @param bool   $isMain
	 * @param string $subKey
	 *
	 * @return bool
	 */
	public function addTable(string $table, bool $isMain = false, string $subKey = '')
	{
		$parse = $this->parseTable($table);
		if ($parse === false)
			return false;
		$key = strtolower($parse['name']);
		$key = $this->addSubKey($key, $subKey);
		
		if (!isset($this->tables[$key])) {
			if ($isMain) {
				$parse['index'] = 1;
				if ($parse['isQuery'])
					$this->useAlias = true;
			} else {
				$parse['index'] = ++$this->tableCounter;
			}
			$this->tables[$key]                   = $parse;
			$this->tableMappings[$key]            = $key;
			$this->tableMappings[$parse['index']] = $key;
			if (!empty($parse['as']))
				$this->tableMappings[$parse['as']] = $key;
		}
		if (!empty($subKey)) {
			return $key;
		}
		return $parse['name'];
	}
	
	/**
	 * 删除表
	 *
	 * @param string $table
	 *
	 * @return $this
	 */
	public function removeTable(string $table)
	{
		if (($table = $this->tableOf($table)) !== false && isset($this->tables[$table])) {
			$tb = $this->tables[$table];
			unset($this->tables[$table],
				$this->tableMappings[$tb['name']],
				$this->tableMappings[$tb['as']],
				$this->tableMappings[$tb['index']]);
		}
		return $this;
	}
	
	/**
	 * 取得表索引名
	 *
	 * @param $table
	 *
	 * @return bool
	 */
	public function tableOf($table)
	{
		$table = strtolower($table);
		if (!empty($this->tableAsPrefix) && stripos($table, $this->tableAsPrefix) === 0) {
			$table = substr($table, strlen($this->tableAsPrefix));
		}
		return $this->tableMappings[$table] ?? false;
	}
	
	/**
	 * 取得表的解析数据
	 *
	 * @param             $table
	 * @param string|null $field
	 *
	 * @return bool|array|string|null
	 */
	public function getTable($table, string $field = null)
	{
		if (($table = $this->tableOf($table)) !== false && isset($this->tables[$table])) {
			if (isset($field))
				return isset($this->tables[$table][$field]) ? $this->tables[$table][$field] : false;
			return $this->tables[$table];
		}
		return false;
	}
	
	/**
	 * 生成表名，针对的是生成如：from table，join table时的表名，会自动拼接table as name
	 *
	 * @param             $table
	 * @param string|null $db
	 *
	 * @return string
	 */
	public function tableName($table, string $db = null): string
	{
		$data = $this->getTable($table);
		if ($data === false)
			return '';
		$name = $data['name'];
		// 拼接db
		if (!empty($data['db']) && $data['db'] !== $db)
			$name = $data['db'] . '.' . $name;
		// 拼接as
		if ($this->isUseAlias())
			$name .= ' ' . (empty($data['as']) ? "{$this->tableAsPrefix}{$data['index']}" : $data['as']);
		return $name;
	}
	
	/**
	 * 基于表名（别名）生成相应的字段名，用于table.field，
	 *
	 * @param        $table
	 * @param string $field
	 * @param string $as
	 *
	 * @return string
	 */
	public function tableField($table, string $field = null, string $as = null): string
	{
		$data = $this->getTable($table);
		if ($data === false)
			return '';
		$name = $this->isUseAlias() ?
			(empty($data['as']) ? "{$this->tableAsPrefix}{$data['index']}" : $data['as']) :
			$data['name'];
		if (!empty($field))
			$name .= '.' . $field;
		if (!empty($as))
			$name .= ' ' . $as;
		return $name;
	}
	
	/**
	 * @return bool 表名是否需要专用别名
	 */
	public function isUseAlias(): bool
	{
		return $this->useAlias || count($this->tables) > 1;
	}
	
	public function setUseAlias(bool $useAlias)
	{
		$this->useAlias = $useAlias;
		return $this;
	}
}