<?php

/**
 * Link类
 * 
 * @author ShuangYa
 * @package Blog
 * @category Library
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=blog&type=license
 */

namespace blog\model;
use \Sy;
use \sy\lib\db\Mysql;
use \blog\libs\template\ListBase;

class Link {
	public function __construct() {
	}
	/**
	 * 获取Link
	 * @access public
	 * @param int $limit 数量
	 * @param int $rel 类型
	 * @return Object(ListBase)
	 */
	public static function getList($limit = '', $rel = '') {
		$sql = 'SELECT * FROM `#@__link` ';
		if (!empty($rel)) {
			$sql .= 'WHERE rel = ? ' . $limit;
			$r = Mysql::i()->query($sql, [$rel]);
		} else {
			$sql .= $limit;
			$r = Mysql::i()->query($sql);
		}
		return new ListBase($r);
	}
	/**
	 * 获取Link数量
	 * @access public
	 * @param int $rel 类型
	 * @return int
	 */
	public static function getNum($rel = '') {
		$sql = 'SELECT count(*) as num FROM `#@__link` ';
		if (!empty($rel)) {
			$sql .= 'WHERE rel = ? ';
			$r = Mysql::i()->getOne($sql, [$rel]);
		} else {
			$r = Mysql::i()->getOne($sql);
		}
		return $r['num'];
	}
	/**
	 * 新增Link
	 * @access public
	 * @param string $title 标题
	 * @param string $rel Rel
	 * @param string $url URL
	 * @return int
	 */
	public static function add($title, $rel, $url) {
		$title = addslashes($title);
		$rel = addslashes($rel);
		$url = addslashes($url);
		$sql = "INSERT INTO `#@__link`(`title`,`rel`,`url`) VALUES ('$title','$rel','$url')";
		Mysql::i()->query($sql);
		return Mysql::i()->getLastId();
	}
	/**
	 * 修改Link
	 * @access public
	 * @param int $id
	 * @param string $title 标题
	 * @param string $rel Rel
	 * @param string $url URL
	 */
	public static function set($id, $title, $rel, $url) {
		$title = addslashes($title);
		$rel = addslashes($rel);
		$url = addslashes($url);
		$sql = "UPDATE `#@__link` SET title = '$title', rel = '$rel', url = '$url' WHERE id = ?";
		Mysql::i()->query($sql, [$id]);
	}
	/**
	 * 删除Link
	 * @access public
	 * @param int $id
	 */
	public static function del($id) {
		Mysql::i()->query('DELETE FROM `#@__link` WHERE id = ?', [$id]);
	}
}
