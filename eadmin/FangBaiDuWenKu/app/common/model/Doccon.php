<?php

namespace app\common\model;

/**
 * 模型
 */
class Doccon extends ModelBase
{
	protected $insert = ['create_time'=>TIME_NOW];
	protected $auto = ['update_time'=>TIME_NOW];
	protected $update = ['update_time'=>TIME_NOW];
	
	
	public function RatyUser()
	{
		return $this->hasMany('RatyUser','itemid','id');
	}
	
	public function getTitleAttr($val)
	{
	
		return html_entity_decode($val);
	}
}
