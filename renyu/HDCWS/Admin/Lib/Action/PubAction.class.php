<?php

class PubAction extends Action {
	
	public function checkLogin(){
	
		if($_SESSION['hd_user']) echo json_encode(array('user' => $_SESSION['hd_user']));
	
		else echo json_encode(array('user' => 0));
	
	}
	
	public function login(){
	
		$data = I('post.');

		if(md5($data['verify']) == $_SESSION['verify']){
		
			$user = D('user');
			
			$u = $user -> field('id,name,email,addtime,lastlogin,lastip') -> where(array('name' => $data['name'], 'password' => md5($data['password']))) -> find();
			
			if(!empty($u)){
				
				$flag = true;
				
				$thislogin = date('Y-m-d H:i:s');
				
				$ip = $_SERVER['REMOTE_ADDR'];
				
				$user -> save(array('id' => $u['id'], 'lastlogin' => $thislogin, 'lastip' => $ip));
				
				$_SESSION['hd_user'] = array_merge($u, array('thislogin' => $thislogin, 'thisip' => $ip, 'isSuper' => ($data['name'] == 'admin' ? 1 : 0)));			
				
				$result = $_SESSION['hd_user'];
				
			}else{
				
				$flag = false;
					
				$result = 0;
				
			}
		
		}else{
			
			$flag = false;
			
			$result = -1;
			
		}
		
		echo json_encode(array('success' => $flag, 'user' => $result));
	
	}
	
	public function logout(){
	
		unset($_SESSION['hd_user']);
	
	}	
	
	public function verify(){
		
		import('ORG.Util.Image');
		
		Image::buildImageVerify(4, 1, 'png', 50, 20);
		
	}

	public function editSelf(){
	
		$flag = true;
	
		$data = I('post.');
		
		$user = D('user');
		
		$id = $_SESSION['hd_user']['id'];

		$msg = '修改失败';
		
		$storeData = array();
		
		if(!empty($id)){
			
			$storeData['id'] = $id;
			
			$storeData['email'] = $data['email'];
		
			if(!empty($data['newpassword'])){
			
				if(empty($data['password'])){
					
					$flag = false;
					
					$msg = '原密码不能为空';
					
				}
		
				if(empty($data['newpassword']) || $data['newpassword'] != $data['renewpassword']){
					
					$flag = false;
					
					$msg = '新密码不一致';
					
				}

				$thisUser = $user -> where(array('id' => $id)) -> find();

				if(empty($thisUser) || $thisUser['password'] != md5($data['password'])){
				
					$flag = false;
					
					$msg = '用户不存在或原密码不匹配';
				
				}
				
				$storeData['password'] = md5($data['newpassword']);

			}

			$result = $user -> save($storeData);

			if(empty($result)) $flag = false;
	
		}else $flag = false;
		
		if($flag) $msg = '修改成功';
		
		echo json_encode(array('success' => $flag, msg => $msg));
	
	}	
	
}