<?php
namespace addons\chinacity\model;

use think\Model;

/**
 * 全国城市乡镇信息模型
 */
class District extends Model{
	
	public function _list($map){
		$order = 'id ASC';
		$data = collection($this->where($map)->order($order)->select())->toArray();
		return $data;
	}
}
