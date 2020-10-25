<?php
namespace Admin\Model;
use Think\Model;

Class AdminModel extends Model{
	
	/**
	 * 获取后台用户
	 * @author jlb
	 * @param  [type] $admin_id [description]
	 * @param  string $field    [description]
	 * @return [type]           [description]
	 */
	public function getAdmin($admin_id, $field='')
	{
		static $adminList = [];
		if ( !empty($adminList[$admin_id]) )
		{
			return $field ? $adminList[$admin_id][$field] : $adminList[$admin_id];
		}

		$list = $this->select();
		foreach ($list as $key => $value) {
			$adminList[$value['admin_id']] = $value;
		}
		return $field ? $adminList[$admin_id][$field] : $adminList[$admin_id];
	}
}