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
namespace Tang\Database\Sql\Schema;
use Iterator;
/**
 * 字段信息管理
 * @author 吉兵
 */
class Columns implements Iterator
{
    /**
     * 字段信息
     * @var array
     */
    protected $items = array();
    /**
     * sequence
     * @var string
     */
    protected $sequence;
    /**
     * 主键
     * @var array
     */
    protected $primaryKeys;
	protected $current = 0;
    /**
     * 是否自动增长
     * @var bool
     */
    protected $incrementing = false;
	public function current()
	{
		return $this->items[$this->current];
	}
	public function next()
	{
		++$this->current;
	}
	public function key()
	{
		return key($this->items);
	}
	public function valid()
	{
		return isset($this->items[$this->current]);
	}
	public function rewind()
	{
		$this->current = 0;
	}
	public function addColumn($columnName,$isPrimaryKey = false,$incrementing = false)
	{
		$this->items[] = $columnName;
		if($isPrimaryKey)
		{
			$this->primaryKeys[] = $columnName;
		}
		if($incrementing)
		{
			$this->incrementing = true;
		}
	}
	public function getIncrementing()
	{
		return $this->incrementing;
	}
	public function getPrimaryKey()
	{
		return $this->primaryKeys[0];
	}
	public function getPrimaryKeys()
	{
		return $this->primaryKeys;
	}
	public function getSequence()
	{
		return $this->sequence;
	}
	public function setSequence($sequence)
	{
		$this->sequence = $sequence;
	}
}