<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 组织结构服务接口
*/

defined('INPOP') or exit('Access Denied');

//组织结构接口
interface _organization{
	//获取组织详情
	static function getInfo();
	//添加组织
	static function doAdd();
	//更新组织
	static function doUpdate();
	//获取组织列表
	static function doList();
	//获取组织总数
	static function doCount();
	//获取分组详情
	static function getGroupInfo();
	//添加分组
	static function addGroup();
	//更新分组
	static function updateGroup();
	//获取分组列表
	static function getGroupList();
	//获取角色列表
	static function getRoleList();
}

?>