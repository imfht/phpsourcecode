<?php 
/*
 * 后台管理员模型
 */
namespace Admin\Model;
use Think\Model;
class AdminRoleModel extends Model {
	
	public function get_role_list(){
		$result = $this->select();
		if(!$result)
			return false;
		//按角色ID来排列数组
		foreach ($result as $v){
			$data[$v[roleid]] = $v;
		}
		//保存到缓存中
		S('role', $data,'3600');
		return $data;
	}
}
