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
class RoleAccess extends \think\Model {

	protected $name = "auth_group_access";

	public function getStatusTextAttr($value, $data) {
		$status = [1 => '开启', 0 => '禁用'];
		return isset($status[$data['status']]) ? $status[$data['status']] : '未知';
	}

	public function getDataList($request) {
		$map = [];

		$list = self::where($map)->paginate($request->pageConfig);
		return $list;
	}

	public static function getRoleByUid($uid) {
		return self::where('uid', $uid)->alias('ra')->field('ra.group_id, r.data_auth,r.api_auth')
			->join('auth_group r', 'r.id = ra.group_id')
			->find();
	}
}