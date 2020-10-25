<?php
namespace blog\libs\template;
use \blog\libs\template\ItemBase;
class ListBase {
	public $list;
	/**
	 * 构造函数
	 * @access public
	 * @param array $list
	 */
	public function __construct($list) {
		$this->list = $list;
		reset($this->list);
	}
	/**
	 * 移到下一个
	 * @access public
	 * @return array
	 */
	public function next() {
		$r = current($this->list);
		next($this->list);
		return $r === FALSE ? FALSE : new ItemBase($r);
	}
	/**
	 * 重新开始
	 * @access public
	 */
	public function reset() {
		reset($this->list);
	}
	/**
	 * 获取全部
	 * @access public
	 * @return array
	 */
	public function getAll() {
		return $this->list;
	}
}
