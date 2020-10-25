<?php 
/**
 *
 * 用户相关模块
 *
 **/ 

class DataAction extends Action {
	
	public function index(){
		echo 1111; die();
	}
	//把用户表数据转到Role表  合并
	public function exportUserToRole(){
		$role = M('role');
		$role_list = $role->select();
		foreach($role_list as $k=>$v){
			if($v['user_id']){
				if($user = M('user')->where('user_id = %d', $v['user_id'])->find()){
					$data = array();
					$data['uname'] = $user['name'];
					$data['category_id'] = $user['category_id'];
					$data['status'] = $user['status'];
					$data['name'] = $user['name'];
					$data['password'] = $user['password'];
					$data['salt'] = $user['salt'];
					$data['sex'] = $user['sex'];
					$data['email'] = $user['email'];
					$data['telephone'] = $user['telephone'];
					$data['address'] = $user['address'];
					$data['navigation'] = $user['navigation'];
					$data['dashboard'] = $user['dashboard'];
					$data['reg_ip'] = $user['reg_ip'];
					$data['reg_time'] = $user['reg_time'];
					$data['last_login_time'] = $user['last_login_time'];
					$data['lostpw_time'] = $user['lostpw_time'];
					$data['weixinid'] = $user['weixinid'];

					if($role->where('role_id = %d', $v['role_id'])->save($data)) echo 'success'.$k;
				}
			}
		}
	}
	
	public function exportRoleToPosition(){
		$role = M('role');
		$position = M('position');
		$role_list = $role->select();
		foreach($role_list as $k=>$v){
			$data = array();
			$data['parent_id'] = $v['parent_id'];
			$data['name'] = $v['name'];
			$data['position_id'] = $v['role_id'];
			$data['department_id'] = $v['department_id'];
			$data['description'] = $v['description'] ? $v['description'] : '';
			$position->add($data); 
			echo 'success '.$k.'<br/>';
		}
	}
}