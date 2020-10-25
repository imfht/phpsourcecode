<?php
namespace Admin\Model;
use Think\Model;

Class RoleModel extends Model{
	
	/**
	 * 获取后台角色
	 * @author jlb
	 * @param  [type] $role_id [description]
	 * @param  string $field    [description]
	 * @return [type]           [description]
	 */
	public function getRole($role_id, $field='')
	{
		static $adminList = [];
		if ( !empty($adminList[$role_id]) )
		{
			return $field ? $adminList[$role_id][$field] : $adminList[$role_id];
		}

		$list = $this->select();
		foreach ($list as $key => $value) {
			$adminList[$value['role_id']] = $value;
		}
		return $field ? $adminList[$role_id][$field] : $adminList[$role_id];
	}
}