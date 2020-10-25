<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Adm;

/**
 * 分页类
 *
 * @package Ke\Adm
 * @property string $field
 * @property int    $size
 * @property int    $current
 * @property int    $total
 * @property int    $recordCount
 */
class Pagination
{

	protected $field = 'page';

	protected $size = 20;

	protected $current = false;

	protected $total = 0;

	protected $recordCount = 0;

	protected $treeSort = null;

	public function __construct($options = null)
	{
		if (isset($options)) {
			$this->setOptions($options);

		}
	}

	public function setOptions($options, int $current = null)
	{
		if (is_int($options)) {
			$this->setSize($options);
			if (isset($current) && is_int($current) && $current > 0)
				$this->setCurrent($current);
		}
		elseif (is_array($options)) {
			if (isset($options['size']))
				$this->setSize($options['size']);
			if (isset($options['current']))
				$this->setCurrent($options['current']);
			if (isset($options['field']))
				$this->setField($options['field']);
			if (isset($options['total']))
				$this->setTotal($options['total']);
		}
		return $this;
	}

	public function setField(string $field)
	{
		$field = trim($field);
		if (!empty($field))
			$this->field = $field;
		return $this;
	}

	public function setSize(int $size)
	{
		if ($size > 0)
			$this->size = $size;
		return $this;
	}

	public function setCurrent(int $current)
	{
		if ($current < 0)
			$current = 1;
		$this->current = $current;
		return $this;
	}

	public function setTotal(int $total)
	{
		$this->total = $total;
		return $this;
	}

	public function __get($field)
	{
		if (isset($this->{$field}))
			return $this->{$field};
		return null;
	}

	public function getField()
	{
		return $this->field;
	}

	public function getSize()
	{
		return $this->size;
	}

	public function getTotal()
	{
		return $this->total;
	}

	public function getRecordCount()
	{
		return $this->recordCount;
	}

	public function prepare(Query $query)
	{
		$newQuery = clone $query;
		$this->recordCount = $newQuery->count();
		$this->total = intval($this->recordCount / $this->size);
		if ($this->recordCount % $this->size > 0)
			$this->total += 1;

		$current = $this->current;
		if ($current === false) {
			if (empty($this->field))
				$current = 1;
			else
				$current = $_GET[$this->field] ?? 1;
		}
		if (!is_numeric($current) || $current <= 0)
			$current = 1;
		elseif ($current >= $this->total)
			$current = $this->total;
		$this->setCurrent($current);

		$query->limit($this->size)->offset(($this->current - 1) * $this->size);
		return $this;
	}

	public function export()
	{
		return [
			'field' => $this->field,
		    'size' => $this->size,
		    'current' => $this->current,
		    'total' => $this->total,
		    'recordCount' => $this->recordCount,
		];
	}
}