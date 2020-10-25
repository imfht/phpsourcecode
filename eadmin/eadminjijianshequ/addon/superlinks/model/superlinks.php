<?php

namespace addon\superlinks\model;

use app\common\model\ModelBase;
/**
 * 导航模型
 */
class superlinks extends ModelBase
{

	public function getStatusAttr($status)
	{
		if($status==1){
			$name='启用';
		}else{
			$name='禁用';
		}
		return $name;
	}
	public function getTypeAttr($type)
	{
		if($type==1){
			$name='图片连接';
		}else{
			$name='普通连接';
		}
		return $name;
	}
}
