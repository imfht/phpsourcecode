<?php

class PubAction extends Action {

	public function login(){
		
		if(!empty($_SESSION['hd_user'])) $this -> redirect('Index/index');
	
		$this -> display();
	
	}
	
	public function form(){

		$username = I('username', '', 'trim');
		
		$password = I('password', '', 'md5');
		
		$verify = I('code', '', 'md5');
		
		$user = D('user');
		
		if($_SESSION['verify'] != $verify){
		
			$this -> error('验证码不正确');
		
		}

		$result = $user -> field('id,name,email,addtime,lastlogin,lastip') -> where(array('name' => $username, 'password' => $password)) -> find();
		
		if(empty($result)){
		
			$this -> error('用户名或密码有误');
		
		}else{

			$result['thislogin'] = date('Y-m-d H:i:s');

			$result['thisip'] = $_SERVER['REMOTE_ADDR'];

			$result['isSuper'] = $username == 'admin' ? 1 : 0;
			
			$_SESSION['hd_user'] = $result;
			
			$user -> save(array('id' => $result['id'], 'lastlogin' => $result['thislogin'], 'lastip' => $result['thisip']));
			
			$this -> redirect('Index/index');

		}
	
	}

	public function logout(){
		
		unset($_SESSION['hd_user']);
	
		$this -> redirect('login');
	
	}
	
	//证码码
	public function verify(){
		
        import('ORG.Util.Image');
        
        //例:Image::buildImageVerify(4, 1, png, 48, 22, 'verify');
        //第二个参数，验证字符串的类型，默认为数字，其他支持类型有0 字母 1 数字 2 大写字母 3 小写字母 4中文 5混合
        //最后一个参数是在seesion中存的参数名
        Image::buildImageVerify(4, 1, 'png', 50, 25, 'verify');

	}

	public function editself(){
	
		$id = intval($_SESSION['hd_user']['id']);
	
		$data = D('user') -> where('id='. $id) -> find();
	
		$this -> assign('data', $data);
	
		$this -> display();
	
	}
	
	public function editselfdata(){
	
		$flag = true;
	
		$data = I('post.');
		
		$user = D('user');
		
		$id = $_SESSION['hd_user']['id'];
		
		$storeData = array();
		
		if(!empty($id)){
			
			$storeData['id'] = $id;
			
			$storeData['email'] = $data['email'];
		
			if(!empty($data['newpassword'])){
			
				if(empty($data['password'])) $this -> error('原密码不能为空');
		
				if(empty($data['newpassword']) || $data['newpassword'] != $data['renewpassword']) $this -> error('新密码不一致');

				$thisUser = $user -> where(array('id' => $id)) -> find();

				if(empty($thisUser) || $thisUser['password'] != md5($data['password'])) $this -> error('用户不存在或原密码不匹配');
				
				$storeData['password'] = md5($data['newpassword']);

			}

			$result = $user -> save($storeData);

			if(empty($result)) $flag = false;
	
		}else $flag = false;
	
		if(!$flag){
	
			$this -> error('修改失败');
	
		}else $this -> success('修改成功');
	
	}		

}