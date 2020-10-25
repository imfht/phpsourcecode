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
namespace Tang\Database\Sql;
/**
 * 解析器基类
 * Class Grammar
 * @package Tang\Database\Sql
 */
abstract class Grammar
{
    /**
     * 表前缀
     * @var string
     */
    protected $tablePrefix;

    /**
     * 设置表前缀
     *
     * @param $tablePrefix
     */
    public function setTablePrefix($tablePrefix)
	{
		$this->tablePrefix = $tablePrefix;
	}

    /**
     * 将字段批量包装后转换为字符串
     * @param array $columns
     * @return string
     */
    public function columnize(array $columns)
	{
		return implode(', ', array_map(array($this,'wrap'), $columns));
	}

    /**
     * 将字段批量包装
     * @param array $values
     * @return array
     */
    public function wraps(array $values)
	{
		return array_map(array($this,'wrap'), $values);
	}

    /**
     * 包装字段
     * @param $value
     * @return string
     */
    public function wrap($value)
	{
		if (strpos(strtolower($value), ' as ') !== false)
		{
			$segments = explode(' ', $value);
			return $this->wrap($segments[0]).' as '.$this->wrap($segments[2]);
		}
		$wrapped = array();
		$segments = explode('.', $value);
		foreach ($segments as $key => $segment)
		{
			if ($key == 0 && count($segments) > 1)
			{
				$wrapped[] = $this->wrapTable($segment);
			}
			else
			{
				$wrapped[] = $this->wrapValue($segment);
			}
		}
		return implode('.', $wrapped);
	}

    /**
     * 将值批量转换为?
     * @param array $values
     * @return string
     */
    public function parameterize(array $values)
	{
		return implode(', ', array_map(function($value){return '?';}, $values));
	}

    /**
     * 包装表
     * @param $table
     * @return string
     */
    protected function wrapTable($table)
	{
		return $this->wrap($this->tablePrefix.$table);
	}

    /**
     * 包装值
     * @param $value
     * @return string
     */
    protected function wrapValue($value)
	{
		if ($value === '*') return $value;
		return '"'.str_replace('"', '""', $value).'"';
	}

    /**
     * 获取默认值
     * @param $value
     * @return string
     */
    protected function getDefaultValue($value)
	{
		if (is_bool($value)) return '\''.intval($value).'\'';
		return '\''.strval($value).'\'';
	}
}