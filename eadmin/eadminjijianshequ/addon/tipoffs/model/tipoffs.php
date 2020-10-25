<?php

namespace addon\tipoffs\model;

use app\common\model\ModelBase;
/**
 * 导航模型
 */
class tipoffs extends ModelBase
{

	public function getStatusAttr($status)
	{
		if ($status == 0) {
			$name = '未处理';
		} elseif ($status == 1) {
			$name = '已处理';
		} else {
			$name = '搁置';
		}

		return $name;
	}
	/*public function getTypeAttr($type)
	{
		if($type==1){
			$name='图片连接';
		}else{
			$name='普通连接';
		}
		return $name;
	}*/
}
