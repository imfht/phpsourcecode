<?php
/**
 * 数据库接口类
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Relational Database
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Sy\DB;

interface DBInterface {
	/**
	 * 执行查询并返回结果
	 * 
	 * @access public
	 * @param string $sql SQL语句
	 * @param array $data 参数预绑定
	 * @return array
	 */
	public function query(string $sql, $data = null);
	/**
	 * 执行查询并返回一条结果
	 * 
	 * @access public
	 * @param string $sql SQL语句
	 * @param array $data 参数预绑定
	 * @return array
	 */
	public function get(string $sql, $data = null);
	/**
	 * 获取Builder对象
	 * 
	 * @access public
	 * @return string/object
	 */
	public static function getBuilder();
}