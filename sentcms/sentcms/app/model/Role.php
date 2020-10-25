<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\model;

/**
 * 角色模型
 */
class Role extends \think\Model {

	protected $name = "auth_group";
	public $type = [
		'api_auth' => 'json',
		'component_auth' => 'json',
	];

	public function getStatusTextAttr($value, $data) {
		$status = [1 => '开启', 0 => '禁用'];
		return isset($status[$data['status']]) ? $status[$data['status']] : '未知';
	}

	public function getDataList($request) {
		$map = [];

		$list = self::where($map)->paginate($request->pageConfig);
		return $list;
	}
	
	public function getUserAuthInfo($request){
		$data = self::where('id', $request->user['role']['group_id'])->find();
		return $data;
	}
}