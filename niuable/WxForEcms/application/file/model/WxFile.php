<?php
namespace app\file\model;
use think\Model;
class WxFile extends Model{
	protected $type = [
			'up_to_wx_time'  =>  'timestamp:Y/m/d H:i:s',
			'update_time'  =>  'timestamp:Y/m/d H:i:s',
			'create_time'  =>  'timestamp:Y/m/d H:i:s',
	];
	/**
	 * getTypeAttr
	 * @method 修改器
	 * @param number $value
	 * @return string 中文版“类型”
	 */
	public function getTypeAttr($value){
		$types=[1=>'图片',2=>'涂鸦','3'=>'音频','4'=>'视频','5'=>'其他'];
		return $types[$value];
	}
	
}