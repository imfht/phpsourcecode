<?php

/**
 * Meta类
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
use \sy\base\Router;
use \sy\lib\db\Mysql;
use \blog\libs\Html;
use \blog\libs\template\ListBase;

class Meta {
	public function __construct() {
	}
	/**
	 * 获取Meta列表
	 * @access public
	 * @param array $param
	 * @param int $param[type] 类型
	 * @param int $param[title] 标题（模糊查询）
	 * @param int $param[limit] 数量
	 * @param int $param[sort] 排序
	 * @return Object(ListBase)
	 */
	public static function getList($param = []) {
		extract($param, EXTR_SKIP);
		$sql = 'SELECT * FROM `#@__meta` ';
		$data = [];
		if (isset($type)) {
			$sql .= 'WHERE type = ? ';
			$data[] = $type;
		}
		if (isset($title)) {
			if (strpos($sql, 'WHERE') === FALSE) {
				$sql .= 'WHERE ';
			} else {
				$sql .= 'AND ';
			}
			$sql .= 'title LIKE ? ';
			$data[] = '%' . $title . '%';
		}
		if (isset($sort)) {
			$sort[1] = strtoupper($sort[1]);
			if (in_array($sort[0], ['id', 'num'], TRUE) && in_array($sort[1], ['ASC', 'DESC'], TRUE)) {
				$sql .= 'ORDER BY ' . $sort[0] . ' ' . $sort[1] . ' ';
			}
		}
		if (isset($limit)) {
			$sql .= 'LIMIT ' . $limit . ' ';
		}
		$r = Mysql::i()->query($sql, $data);
		return new ListBase($r);
	}
	/**
	 * 获取Meta数量
	 * @access public
	 * @param int $rel 类型
	 * @return int
	 */
	public static function getNum($title = '') {
		$sql = 'SELECT count(*) as num FROM `#@__meta` ';
		if (!empty($rel)) {
			$sql .= 'WHERE title LIKE ? ';
			$r = Mysql::i()->getOne($sql, ['%' . $title . '%']);
		} else {
			$r = Mysql::i()->getOne($sql);
		}
		return $r['num'];
	}
	/**
	 * 输出Tags
	 * @access public
	 * @param string $tags
	 */
	public static function tags($tags) {
		$tags = explode(',', $tags);
		$r = '';
		foreach ($tags as $tag) {
			$r .= '<a href="' . Router::createUrl(['index/article/list', 'type' => 'tag', 'val' => $tag, 'page' => 1]) . '">' . Html::encode($tag) . '</a>,';
		}
		$r = rtrim($r, ',');
		$r = str_replace(',', ',&nbsp;', $r);
		return $r;
	}
	/**
	 * 获取Meta信息
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public static function get($id) {
		return Mysql::i()->getOne("SELECT * FROM `#@__meta` WHERE id = ?", [$id]);
	}
	/**
	 * 靠名称获取Meta信息
	 * @access public
	 * @param string $name
	 * @return array
	 */
	public static function getByName($name) {
		return Mysql::i()->getOne("SELECT * FROM `#@__meta` WHERE title = ?", [$name]);
	}
	/**
	 * 增加一个Meta(Tag或分类)
	 * 返回-1表示已存在同名meta
	 * @access public
	 * @param string $title
	 * @param int $type 类型
	 * @return int
	 */
	public static function add($title, $type = 2) {
		$meta = static::getByName($title);
		if (!empty($meta['id'])) {
			return - 1;
		}
		$title = addslashes($title);
		Mysql::i()->query("INSERT INTO `#@__meta`(`title`,`type`) VALUES ('$title','$type')");
		return Mysql::i()->getLastId();
	}
	/**
	 * 修改一个Meta(Tag或分类)
	 * @access public
	 * @param int $id
	 * @param string $title
	 * @param int $type 类型
	 */
	public static function set($id, $title, $type = 2) {
		$type = intval($type);
		$title = addslashes($title);
		Mysql::i()->query("UPDATE `#@__meta` SET title = '$title', type = '$type' WHERE id = ?", [$id]);
	}
}
