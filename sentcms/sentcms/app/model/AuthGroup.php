<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\model;

use think\Model;

/**
* 设置模型
*/
class AuthGroup extends Model{

	public $keyList = [
		['name'=>'id', 'title'=>'ID', 'type'=>'hidden', 'help'=>'', 'option'=>''],
		['name'=>'module', 'title'=>'所属模块', 'type'=>'hidden', 'help'=>'', 'option'=>''],
		['name'=>'title', 'title'=>'用户组名', 'type'=>'text', 'is_must'=> true, 'help'=>'', 'option'=>''],
		['name'=>'description', 'title'=>'分组描述', 'type'=>'textarea', 'help'=>'', 'option'=>''],
		['name'=>'status', 'title'=>'状态', 'type'=>'select', 'help'=>'', 'option'=> [['key' => 0, 'label' => '禁用'],['key' => 1, 'label' => '启用']]],
	];

	protected function getRulesAttr($value){
		return $value ? explode(",", $value) : [];
	}

	public static function getAuthModels($uid){}
}