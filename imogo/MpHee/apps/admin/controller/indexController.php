<?php
class indexController extends adminController{
	
	public function index(){
		$userinfo = $this->userinfo;
		$ppid = $this->ppid;
		if( $userinfo['pid'] == 0){
		if( empty($ppid) ){
			$this->leftMenu = api(array('appmanage','ppacount'), 'getMenu');
		}else{
			$this->leftMenu = api(getApps(array('ppacount')), 'getMenu');
		}
		}else{
		$manage = json_decode($userinfo['manage'],true);
		if( count($manage) > 1 ){
			if( empty($ppid) ){
				$this->leftMenu = api(array('ppacount'), 'getMenu');
			}else{
				for($i=0;$i<count($manage);$i++){
					if($manage[$i]['ppid'] == $ppid){
						$this->leftMenu = api($manage[$i]['action'], 'getMenu');
						break;
					}
				}
			}
		}else{
			if( empty($ppid) ){
				$selectppid = $manage[0]['ppid'];
				$this->redirect( url('ppacount/index/ppacountselect',array('id'=>$selectppid)) ,true);
			}else{
				$this->leftMenu = api($manage[0]['action'], 'getMenu');
			}
		}
		}
		if( empty($ppid) ){
			$this->pplist = $this->model->table('ppacount')->where( array('adminid'=>$userinfo['id']) )->select();
		}else{
			$this->pplist = $this->model->query( 'select id,name from {pre}ppacount where adminid = "'.$userinfo['id'].'" and id <> "'.$ppid.'"' );
		}
		$this->title = config('admin_title');
		$this->iframeUrl = empty($_GET['callback']) ? url('index/welcome') : $_GET['callback'];
		$this->display();
	}
	
	public function welcome(){
		$this->display();
	}
	
	//登录
	public function login(){
		if( !$this->isPost() ){
			$this->title = config('login_title');
			$this->footer = config('login_footer');
			$this->display();
		}else{
			$result = array('status'=>0, 'msg'=>'登录失败');
			
			$username = trim( $_POST['username'] );
			$password = trim( $_POST['password'] );
			$checkcode = trim( $_POST['checkcode'] );
			
			$msg = Check::rule( array(
						array( Check::must($username), '请输入用户名'),
						array( Check::must($password), '请输入密码'),
						array( Check::must($checkcode), '请输入验证码'),
						array( Check::same($checkcode, $_SESSION['verify']), '验证码不对'),
					));
					
			if( true === $msg ){
				$userinfo = model('admin')->getUserInfo( array('username'=>$username) );
				if( !empty($userinfo) && $userinfo['password'] == md5($password) ){
					$this->setLogin( $userinfo );
					$result['status'] = 1;
					$msg = '登录成功';
				}else{
					$msg = '用户名或密码不对';
				}
			}
			
			$result['msg'] = $msg;
			echo  json_encode($result);
		}
	}
	
	//退出登录
	public function logout(){
		$this->clearLogin( url('index/login') );
	}
	
	//退出当前公众帐号登录
	public function pplogout(){
		set_session('ppinfo',null);
		$this->redirect( url('index/index') ,true);
	}
	
	//生成验证码
	public function verify(){
		Image::buildImageVerify();
	}
	
	//更新缓存
	public function clearCache(){
		api(getApps(), 'clearCache');
		$this->alert('缓存更新成功');	
	}
	
	//修改密码
	public function password(){
		if( !$this->isPost() ){
			$this->display();
		}else{
			$userinfo = $this->userinfo;
			$oldpwd = trim( $_POST['oldpwd'] );
			$newpwd1 = trim( $_POST['newpwd1'] );
			$newpwd2 = trim( $_POST['newpwd2'] );
			
			$msg = Check::rule( array(
						array( Check::must($oldpwd), '请输入原密码'),
						array( Check::must($newpwd1), '请输入新密码'),
						array( Check::must($newpwd2), '请再次输入新密码'),
						array( Check::same($newpwd1, $newpwd2), '两次新密码不相同'),
					));
					
			if( true === $msg ){
				$userinfo = model('admin')->getUserInfo( array('username'=>$userinfo['username']) );
				if( !empty($userinfo) && $userinfo['password'] == md5($oldpwd) ){
					$updatepassword = $this->model->table('admin')->data( array('password'=>md5($newpwd1) ) )->where(array('username'=>$userinfo['username']))->update();
					if($updatepassword){
						$this->alert('修改成功',url('index/logout'),true);
					}else{
						$msg = '修改失败';
					}
				}else{
					$msg = '原密码错误';
				}
			}
			$this->alert( $msg );
		}
	}
	
	//文件上传
	public function upload(){
		$ppid = $this->ppid;
		if( empty( $this->userinfo['username'] ) ){
			echo '没有登录';
		}else{
			if( empty( $ppid ) ){
				$user = $this->userinfo['username'].'_common';
			}else{
				$user = $this->userinfo['username'].'_'.$ppid;
			}
			echo json_encode( $this->_upload( $user ) );
		}
	}
		
}