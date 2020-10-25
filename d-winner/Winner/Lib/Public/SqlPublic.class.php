<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, d-winner, Inc.
 * @link		http://www.d-winner.com
 */

//数据库备份与还原控制类
class SqlPublic extends Action {
	//获取数据表
	public function getTable(){
		$db = M();
		$info = $db->query('show tables from '.C('DB_NAME'));
		$infos = array();
		foreach($info as $a){
			$infos[] = $a['Tables_in_'.C('DB_NAME')];
		}
		return $infos;
		unset($info,$infos);
	}
	
	//获取数据表结构
	public function getField($table){
		$db = M();
		$info = $db->query('show create table '.$table);
		return $info[0]['Create Table'];
		unset($info);
	}
	
	////格式化数据库数据
	public function getData($table,$row,$model='REPLACE'){
		$sql = $model.' INTO `'.str_replace(C('DB_PREFIX'),'#@_',$table).'` VALUES (';
		$values = '';
		foreach($row as $val){
			$val = str_replace(';','&#59',$val);
			$values .= "'".addslashes($val)."',";
		}
		$sql .= substr($values,0,-1).");\r\n";
		return $sql;
		unset($values);
	}
}