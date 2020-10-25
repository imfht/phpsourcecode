<?php

namespace addon\superlinks\model;
use app\common\model\ModelBase;
/**
 * 导航模型
 */
class superlinks extends ModelBase
{
	protected $insert = ['create_time'=>TIME_NOW];
	protected $auto = ['update_time'=>TIME_NOW];
	protected $update = ['update_time'=>TIME_NOW];
	protected function getStatusAttr($value)
	{
		if($value==1){
			$name='启用';
		}else{
			$name='禁用';
		}
		return $name;
	}
	protected function getTypeAttr($value)
	{
		if($value==1){
			$name='图片连接';
		}else{
			$name='普通连接';
		}
		return $name;
	}
}
