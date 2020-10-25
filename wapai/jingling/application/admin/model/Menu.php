<?php
// +----------------------------------------------------------------------
// |   精灵后台系统 [ 基于TP5，快速开发web系统后台的解决方案 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 - 2017 http://www.apijingling.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: wapai 邮箱:wapai@foxmail.com
// +----------------------------------------------------------------------
namespace app\admin\model;
use think\Model;

/**
 * 菜单模型
 */

class Menu extends Model { 
	protected $autoWriteTimestamp = false;
	protected $auto = ['title'];
	// 新增
	protected $insert = ['status'=>1];
	//属性修改器
	protected function setTitleAttr($value, $data)
	{
		return htmlspecialchars($value);
	} 
}