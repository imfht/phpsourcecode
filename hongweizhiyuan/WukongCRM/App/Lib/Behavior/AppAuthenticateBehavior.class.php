<?php 

class AppAuthenticateBehavior extends Behavior {
	protected $options = array();
	
	public function run(&$params) {
		$m = MODULE_NAME;
		$a = ACTION_NAME;
		$allow = $params['allow'];
		$permission = $params['permission'];
		/*
		if(!session('?user_id') && intval(cookie('user_id')) != 0 && trim(cookie('name')) != '' && trim(cookie('salt_code')) != ''){
			$user = M('user')->where(array('user_id' => intval(cookie('user_id'))))->find();
			if (md5(md5($user['user_id'] . $user['name']).$user['salt']) == trim(cookie('salt_code'))) {
				$d_role = D('RoleView');
				$role = $d_role->where('user.user_id = %d', $user['user_id'])->find();
				if($user['category_id'] == 1){
					session('admin', 1);
				}
				session('role_id', $role['role_id']);
				session('position_id', $role['position_id']);
				session('role_name', $role['role_name']);
				session('department_id', $role['department_id']);
				session('name', $user['name']);
				session('user_id', $user['user_id']);
			}
		}
		*/
		
		if (session('?admin')) {
			return true;
		}
		if (in_array($a, $permission)) {	
			return true;
		} elseif (session('?position_id') && session('?role_id')) {
			if (in_array($a, $allow)) {				
				return true;
			} else {
				switch ($a) {
					case "listdialog" : $a = 'index'; break;
					case "adddialog" : $a = 'add'; break;
					case "excelimport" : $a = 'add'; break;
					case "excelexport" : $a = 'view'; break;
					case "cares" :  $a = 'index'; break;
					case "caresview" :  $a = 'view'; break;
					case "caresedit" :  $a = 'edit'; break;
					case "caresdelete" :   $a = 'delete'; break;
					case "caresadd" :  $a = 'add'; break;
					case "receive" : $a = 'add'; break;
					case "role_add" : $a = 'add';break;
					case "sendsms" : $a = 'marketing';break;
					case "sendemail" : $a = 'marketing';break;
 				}
				$url = strtolower($m).'/'.strtolower($a);
				$ask_per = M('permission')->where('url = "%s" and position_id = %d', $url, session('position_id'))->find();
				if (is_array($ask_per) && !empty($ask_per)) {
					return true;
				} else {
					$url = empty($_SERVER['HTTP_REFERER']) ? U('index/index') : $_SERVER['HTTP_REFERER'];
					//alert('error', '您没有此权利!', $url);
					echo json_encode(array('status'=>-2,'info'=>'您没有此权利!'));die();
					
				}
			}
		} else {
			echo json_encode(array('status'=>'-1','info'=>'请先登录!'));die();
			//alert('error',  '请先登录...', U('user/login'));
			
			
		}
	}
}