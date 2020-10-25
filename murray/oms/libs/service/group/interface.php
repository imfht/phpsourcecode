<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 服务接口
*/

defined('INPOP') or exit('Access Denied');

//内容接口
interface _group{
	//获取详情
	static function getInfo();
	//添加
	static function doAdd();
	//更新状态
	static function updateStatus();
	//获取列表
	static function doList();
}

?>