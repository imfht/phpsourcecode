<?php
class webController extends baseController{
	
	public function register(){
		if( !$this->isPost() ){
			$this->title = config('login_title');
			$this->footer = config('login_footer');
			$this->display();
		}else{
			$username = trim( $_POST['username'] );
			$password = trim( $_POST['password'] );
			$passwordagain = trim( $_POST['passwordagain'] );
			$checkcode = trim( $_POST['checkcode'] );
			
			$msg = Check::rule( array(
						array( Check::must($username), '请输入用户名'),
						array( Check::must($password), '请输入密码'),
						array( Check::must($passwordagain), '请再次输入密码'),
						array( Check::must($checkcode), '请输入验证码'),
						array( Check::same($checkcode, $_SESSION['verify']), '验证码不对'),
					));
			$data['username'] = $username;
			$data['password'] = md5($password);
			$data['pid'] = 0;
			$data['createtime'] = time();
			if( true === $msg ){
				if( $this->model->table('admin')->data($data)->insert() ){
					$this->alert('注册成功',url('index/login'),true);
				}else{
					$this->alert('注册失败，请联系管理员');
				}
			}
			$this->alert($msg);
		}
	}
	
	public function validusername(){
		$username = $_POST['param'];
		if( model('admin')->getUserInfo( array('username'=>$username) ) ){
			echo "此用户名已经存在";
		}else{
			echo '{"info":"用户名可用！","status":"y"}';
		}
	}
}