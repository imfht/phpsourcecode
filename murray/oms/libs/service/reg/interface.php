<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 服务注册接口
*/

defined('INPOP') or exit('Access Denied');

//服务注册接口
interface _reg{
	//获取注册服务详情
	static function getInfo();
	//添加注册服务
	static function doAdd();
	//更新注册服务
	static function doUpdate();
	//获取注册服务列表
	static function doList();
	//获取注册服务总数
	static function doCount();
}

?>