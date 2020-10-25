<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 模型服务接口
*/

defined('INPOP') or exit('Access Denied');

//模型接口
interface _former{
	//获取模型详情
	static function getInfo();
	//添加模型
	static function doAdd();
	//更新模型
	static function doUpdate();
	//更新模型状态
	static function updateStatus();
	//获取模型列表
	static function doList();
	//获取模型总数
	static function doCount();
	//获取原型详情
	static function getPrototypeInfo();
	//添加原型
	static function addPrototype();
	//更新原型
	static function updatePrototype();
	//获取原型列表
	static function getPrototypeList();
	//获取工作流详情
	static function getWorkflowInfo();
	//根据条件获取工作流详情
	static function getWorkflowInfoBy();
	//添加工作流
	static function addWorkflow();
	//更新工作流
	static function updateWorkflow();
	//获取工作流列表
	static function getWorkflowList();
	//根据工作流获取原型列表
	static function getPrototypeListByWorkflow();
	//获取字段详情
	static function getFieldInfo();
	//添加字段
	static function addField();
	//更新字段
	static function updateField();
	//获取字段列表
	static function getFieldList();
	//更改数据缓存表
	static function alterCacheTable();
	//添加缓存表数据
	static function addCacheTable();
	//更新缓存表数据
	static function updateCacheTable();
	//获取缓存表列表
	static function getListFromCacheTable();
	//获取缓存表详情
	static function getInfoFromCacheTable();
	//获取缓存数据总数
	static function getCacheTableCount();
}

?>