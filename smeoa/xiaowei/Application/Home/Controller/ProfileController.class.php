<?php
/*--------------------------------------------------------------------
  小微OA系统 - 让工作更轻松快乐 

  Copyright (c) 2013 http://www.smeoa.com All rights reserved.                                             

   

  Author:  jinzhu.yin<smeoa@qq.com>                         

  Support: https://git.oschina.net/smeoa/xiaowei               
--------------------------------------------------------------*/

namespace Home\Controller;

class ProfileController extends HomeController {
	protected $config=array('app_type'=>'personal');
	
	function index(){	
		$plugin['date'] = true;
		$this -> assign("plugin", $plugin);
		$user=D("UserView")->find(get_user_id());
		$this->assign("vo",$user);
		$this->display();
		
	}

	//重置密码
	public function reset_pwd(){
		$id = get_user_id();
		$password = $_POST['password'];
		if ('' == trim($password)) {
			$this -> error('密码不能为空！');
		}
		$User = M('User');
		$User -> password = md5($password);
		$User -> id = $id;
		$result = $User -> save();		
		if (false !== $result) {
			if (C('LDAP_LOGIN')) {
				import("Home.ORG.Util.Ldap");
				$ldap_server = C('LDAP_SERVER');
				$ldap_port = C('LDAP_PORT');
				$ldap_user = C('LDAP_USER');
				$ldap_pwd = C('LDAP_PWD');
				$ldap_base_dn = C('LDAP_BASE_DN');

				$ldap = new \Ldap($ldap_server, $ldap_port, $ldap_user, $ldap_pwd, $ldap_base_dn);
				$emp_no = get_emp_no($id);
				$where_dept['id'] = array('eq', $id);
				$dept_id = M("User") -> where($where_dept) -> getField('dept_id');
				$dept_name = get_dept_name($dept_id);

				$ldap -> reset_pwd($emp_no, $dept_name, $password);
				if (!$ldap -> status) {
					$this -> error($ldap -> info);
				}				
			}			
			$this -> assign('jumpUrl', get_return_url());
			$this -> success("密码修改成功");
		} else {
			$this -> error('重置密码失败！');
		}
	}

	public function password(){	
		$this -> display();
	}

	function save(){
		$model = D("User");
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}
		session('user_pic', $model->pic);
		// 更新数据
		$list = $model -> save();
		if (false !== $list) {
			//成功提示
			$this -> assign('jumpUrl', get_return_url());
			$this -> success('编辑成功!');
		} else {
			//错误提示
			$this -> error('编辑失败!');
		}
	}
}
?>