<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 */
namespace Home\Controller;
use Common\Controller\UserBaseController;
use User\Api\UserApi;
use Org\Util\Input;
class UserController extends UserBaseController {
	public function _initialize() {
		parent::_initialize ();
		$this->user_mod = D ( 'Common/User' );
		$this->role_mod = M ( 'user_role' );
			// 访问者控制
		if (!$this->visitor && in_array ( ACTION_NAME, array (
				'setbase',
				'setcity',
				'setdoname',
				'setface',
				'setpassword',
		))){
			$this->redirect ( 'home/user/login' );
		} else {
			$this->userid = $this->visitor['userid'];
		}
	}
	public function setbase() {
		if (IS_POST) {
			foreach ( $_POST as $key => $val ) {
				$_POST [$key] = Input::deleteHtmlTags ( $val );
			}
			$data ['sex'] = I( 'post.sex','0','intval');
			$data ['signed'] = I( 'post.signed');
			$data ['address'] = I( 'post.address','', 'trim' );
			$data ['phone'] = I( 'post.phone','', 'trim' );
			$data ['blog'] = I( 'post.blog');
			$data ['about'] = I( 'post.about');
			
			if (false !== $this->user_mod->where (array ('userid' =>$this->userid))->save ( $data )) {		
				$this->success ( L ( 'user_info' ) . L ( 'edit_success' ) );
			} else {
				$this->error ( L ( 'user_info' ) . L ( 'edit_failed' ) );
			}
		} else {
			
			$info = $this->user_mod->get(); 
			$strarea = D ( 'Common/Area' )->getArea ($info['areaid']);
			$this->assign ( 'info', $info );
			$this->assign ( 'strarea', $strarea );
			$this->_config_seo (array('title'=>'基本设置','subtitle'=>'用户'));
			$this->display ();
		}
	
	}
	public function setface() {
		if (IS_POST) {
			
			if (! empty ( $_FILES ['picfile'] )) {
				$data_dir = date ( 'Y/md/H/' );
				$file_name = md5 ($this->userid);
				//会员头像规格
				$avatar_size = explode(',', C('ik_avatar_size'));
	            //会员头像保存文件夹
	            $uid = abs(intval($this->userid));
	            $suid = sprintf("%09d", $uid);
	            $dir1 = substr($suid, 0, 3);
	            $dir2 = substr($suid, 3, 2);
	            $dir3 = substr($suid, 5, 2);
	            $avatar_dir = $dir1.'/'.$dir2.'/'.$dir3.'/';
	            //上传头像  新版上传
/*	            $result = savelocalfile($_FILES['picfile'],'face/'.$avatar_dir,array('width'=>C('ik_avatar_size'),'height'=>C('ik_avatar_size')),
	            		array('jpg','gif','png'),
	            		md5($uid));*/
	            $result = \Common\Util\Upload::saveLocalFile(
		            'face/'.$avatar_dir, 
		            array('width'=>C('ik_avatar_size'),'height'=>C('ik_avatar_size')),
		            array('md5', $uid), 1);
			    if ($result['error']) {
	                $this->error($result['error']);
	            } else {
					$this->success('头像修改成功！');
	            }	
			}
			
		}else{
			$this->_config_seo (array('title'=>'会员头像','subtitle'=>'用户'));
			$this->display ();			
		} 
	}
	public function setdoname() {
		$userid = $this->userid;
		$user_mod = new UserApi;
		$strUser = $user_mod->info($userid);
		if (IS_POST) {
			$doname = I('post.doname','','trim');
			if(empty($doname))
			{
				$this->error ("个性域名不能为空！");
			}else if(strlen($doname)<2)
			{
				$this->error ("个性域名至少要2位数字、字母、或下划线(_)组成！");
			
			}else if(!preg_match ( '/^[a-zA-Z]{1}[a-zA-Z0-9\-_]{0,14}$/', $doname ))
			{
				$this->error ("个性域名必须是数字、字母或下划线(_)组成！");
			}
			$ishave = $user_mod->checkDoname($doname);
			if(!$ishave)
			{
				$this->error ("该域名已经被其他人抢注了,请试试别的吧！");
			}else{
				$user_mod->updateInfo($userid, array('doname'=>$doname));
				//修改user modle
				$this->user_mod->where(array('userid'=>$userid))->setField('doname',$doname);
				$this->error ("个性域名修改成功！");
			}
		
		} else {
			$this->assign ( 'doname', $strUser[4] );
			$this->_config_seo (array('title'=>'个性域名','subtitle'=>'用户'));
			$this->display ();	
		}
	}
	public function setcity() {
		$user_mod = $this->user_mod;
		if (IS_POST) {
			$oneid = I( 'post.oneid',0,'intval' );
			$twoid = I( 'post.twoid', 0,'intval' );
			$threeid = I( 'post.threeid',0, 'intval' );
			
			if ($oneid != 0 && $twoid == 0 && $threeid == 0) {
				$areaid = $oneid;
			} elseif ($oneid != 0 && $twoid != 0 && $threeid == 0) {
				$areaid = $twoid;
			} elseif ($oneid != 0 && $twoid != 0 && $threeid != 0) {
				$areaid = $threeid;
			} else {
				$areaid = 0;
			}
			if (false !== $user_mod->where ( array (
					'userid' => $this->visitor['userid'] 
			) )->save ( array (
					'areaid' => $areaid 
			) )) {
				$this->success ( L ( 'user_area' ) . L ( 'edit_success' ) );
			} else {
				$this->error ( L ( 'user_area' ) . L ( 'edit_failed' ) );
			}
		
		} else {
			$areaid = $user_mod->get('areaid');
			$area_mod = D( 'Common/Area' );
			$strarea = $area_mod->getArea ( $areaid);
			// 调出省份数据
			$arrOne = $area_mod->getReferArea ( 0 );
			
			$this->assign ( 'strarea', $strarea );
			$this->assign ( 'arrOne', $arrOne );
			$this->_config_seo (array('title'=>'常居地修改','subtitle'=>'用户'));
			$this->display ();
		}
	}
	public function area() {
		$type = $this->_get ( 'ik' );
		$oneid = $this->_get ( 'oneid' );
		$area_mod = D ( 'Common/Area' );
		switch ($type) {
			case 'two' :
				$arrArea = $area_mod->getReferArea ( $oneid );
				if ($arrArea) {
					echo '<select id="twoid" name="twoid" class="txt">';
					echo '<option value="0">请选择</option>';
					foreach ( $arrArea as $item ) {
						echo '<option value="' . $item ['areaid'] . '">' . $item ['areaname'] . '</option>';
					}
					echo "</select>";
				} else {
					echo '';
				}
				break;
			
			case 'three' :
				$twoid = $this->_get ( 'twoid' );
				$arrArea = $area_mod->getReferArea ( $twoid );
				if ($arrArea) {
					echo '<select id="threeid" name="threeid" class="txt">';
					echo '<option value="0">请选择</option>';
					foreach ( $arrArea as $item ) {
						echo '<option value="' . $item ['areaid'] . '">' . $item ['areaname'] . '</option>';
					}
					echo "</select>";
				} else {
					echo '';
				}
				break;
		}
	}
	
	//重设密码
	public function setpassword() {
		$userid = $this->userid;
		if ( !is_login() ) {
			$this->error( '你应该出发去火星报到啦。',U('User/login') );
		}			
		if (IS_POST) {
			//获取参数
            $uid        =   is_login();
            $password   =   I('post.old');
            $repassword =   I('post.repassword');
            $data['password'] = I('post.password');
            empty($password) && $this->error('请输入原密码');
            empty($data['password']) && $this->error('请输入新密码');
            empty($repassword) && $this->error('请输入确认密码');

            if($data['password'] !== $repassword){
                $this->error('您输入的新密码与确认密码不一致');
            }

            $Api = new UserApi();
            $res = $Api->updatePassword($uid, $password, $data);
            if($res['status']){
                $this->success('修改密码成功！');
            }else{
                $this->error($res['info']);
            }
			
		} else {
			//下期开发第三方登录
/*			$count_user_bind = M('user_bind')->where(array('uid'=>$userid))->count('*');
			if($count_user_bind>0 &&  md5('000000') == $strUser['password']){
				$ispassword = false;
			}else{
				$ispassword = true;
			}*/
			$this->assign('ispassword',true);
			$this->_config_seo (array('title'=>'密码修改','subtitle'=>'用户'));
			$this->display ();
		}
	}
	
	public function login($email = '', $password = '') {
		$this->visitor && $this->redirect ( 'space/index/index', array (
				'id' => $this->visitor['doname'] 
		) );		
		if (IS_POST) {
			/* 调用UC登录接口登录 */
			$user = new UserApi;
			$uid = $user->login($email, $password);
			if(0 < $uid){ //UC登录成功
				/* 登录用户 */
				$Member = D('Common/User');
				if($Member->login($uid)){ //登录用户
					// 跳转到登陆前页面（执行同步操作）
					$ret_url = $this->_post ( 'ret_url', 'trim', C('ik_site_url'));
					$this->redirect($ret_url);
				} else {
					$this->error($Member->getError());
				}

			} else { //登录失败
				switch($uid) {
					case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
					case -2: $error = '密码错误！'; break;
					default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
				}
				$this->error($error);
			}
		} else {
			// 来路
			$ret_url = isset ( $_SERVER ['HTTP_REFERER'] ) && strpos('register', $_SERVER ['HTTP_REFERER']) == -1 ? $_SERVER ['HTTP_REFERER'] : C('ik_site_url');
			$this->assign ( 'ret_url', $ret_url );
			
			$this->_config_seo ( array (
					'title' => '会员登录'
			) );
			$this->display ();
		}
	}
	//2014年3月17日修改用户注册 增加了新的ikmd5验证
	public function register($username = '', $password = '', $repassword = '', $email = '', $authcode = ''){
		$this->visitor && $this->redirect ( 'space/index/index', array ('id' => $this->visitor['doname']));	
			
	    if(C('ik_isinvite') == '2'){
            $this->error('注册已关闭');
        }
        if(IS_POST){ 
        	//弹出 快捷注册
			$simple = I( 'get.simple', 0, 'trim,intval');
			
        	//注册用户
        	/* 检测验证码 */
			if(!check_verify($authcode)){
				$this->error('验证码输入错误！');
			}
			/* 检测密码 */
            //简单注册没有二次密码
			if($simple == 0){
				if ($password != $repassword) {
					$this->error('密码和重复密码不一致！');
				}
			}						

			/* 调用注册接口注册用户 */
            $User = new UserApi;
			$uid = $User->register($username, $password, $email);
			if(0 < $uid){ //注册成功
				//TODO: 发送验证邮件
				$this->success('注册成功！',U('login'));
			} else { //注册失败，显示错误信息
				$this->error($this->showRegError($uid));
			} 
       	
        }else{
        	$this->_config_seo ( array (
				'title' => '会员注册'
			) );
        	$this->display();
        }
	}
	/**
	 * 获取用户注册错误信息
	 * @param  integer $code 错误编码
	 * @return string        错误信息
	 */
	private function showRegError($code = 0){
		switch ($code) {
			case -1:  $error = '用户名长度必须在16个字符以内！'; break;
			case -2:  $error = '用户名被禁止注册！'; break;
			case -3:  $error = '用户名被占用！'; break;
			case -4:  $error = '密码长度必须在6-30个字符之间！'; break;
			case -5:  $error = '邮箱格式不正确！'; break;
			case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
			case -7:  $error = '邮箱被禁止注册！'; break;
			case -8:  $error = '邮箱被占用！'; break;
			case -9:  $error = '手机格式不正确！'; break;
			case -10: $error = '手机被禁止注册！'; break;
			case -11: $error = '手机号被占用！'; break;
			default:  $error = '未知错误';
		}
		return $error;
	}	
	/**
	 * 检测用户
	 */
	public function checkUser() {
		$type = $this->_get ( 't' );
		$user_mod = new UserApi;
		switch ($type) {
			case 'email' :
				$email = $this->_get ( 'email', 'trim' );
				echo $user_mod->email_exists ( $email ) ? 'false' : 'true';
				break;
			
			case 'username' :
				$username = $this->_get ( 'username', 'trim' );
				echo $user_mod->username_exists ( $username ) ? 'false' : 'true';
				break;
		}
	}
	/**
	 * 用户退出
	 */
	public function logout() {
		if(is_login()){
			D('Common/User')->logout();
			$this->success('退出成功！', U('User/login'));
		} else {
			$this->redirect('User/login');
		}
	}
	/* 验证码，用于登录和注册 */	
	public function captcha() {
		$verify = new \Think\Verify();
		$verify->entry(1);
	}	
	//ajax登录
	public function ajaxlogin(){
		//统计用户数
		$count_user = $this->user_mod->count('*');
		$this->assign ( 'count_user', $count_user );		
		$jump = $_SERVER['HTTP_REFERER'];
		$this->assign('jump',$jump);
		$this->display('login_form');	
	}
	//ajax注册	
	public function ajaxregister(){
		//统计用户数
		$count_user = $this->user_mod->count('*');
		$this->assign ( 'count_user', $count_user );	
		$jump = $_SERVER['HTTP_REFERER'];
		$this->assign('jump',$jump);
		$this->display('reg_form');
		
	}
	
	//找回密码服务
	public function forgetpwd(){
		if(IS_POST){
			$email	= $this->_post('email','trim');
			$emailNum = $this->user_mod->where(array('email'=>$email))->find();

			if($email==''){
				$this->error('Email输入不能为空^_^');
			}elseif(empty($emailNum)){
				$this->error('Email不存在，你可能还没有注册^_^');
			}else{
			
				//随机MD5加密
				$resetpwd = md5(rand());
			
				$this->user_mod->where(array('email'=>$email))->setField('resetpwd', $resetpwd);
				
				$subject = C('ik_site_title').'会员密码找回';
				
				$reseturl = C('ik_site_url').'index.php?app=home&c=user&a=resetpwd&mail='.$email.'&set='.$resetpwd;
				$content = '您的登录信息：<br />Email：'.$email.'<br />重设密码链接：<br /><a href="'.$reseturl.'">'.$reseturl.'</a>';
				
				$mailObject = new \Common\Util\Mail(F('setting'));
				
				$result = $mailObject->postMail($email, $subject, $content);
				if($result == '0'){
					$this->error("找回密码所需信息不完整^_^");
				}elseif($result == '1'){
					$this->error("系统已经向你的邮箱发送了邮件，请尽快查收^_^");
				}
					
			}
		}
		$this->_config_seo ( array (
				'title' => '找回密码'
		) );
		$this->display();
	}

	//重设密码
	public function resetpwd(){

		if(IS_POST){
			$email 	= I('post.email');
			$pwd 	= I('post.pwd');
			$repwd	= I('post.repwd');
			$resetpwd = I('post.resetpwd');
			
			if($email=='' || $pwd=='' || $repwd=='' || $resetpwd==''){
				$this->error("你应该去火星生活啦！");
			}elseif($pwd != $repwd){
				$this->error("2次密码输入不一致！");
			}elseif(strlen($pwd)<6){
				$this->error("密码至少要6位！");
			}
				
			$strUser = $this->user_mod->where(array('email'=>$email,'resetpwd'=>$resetpwd))->find();
			if(empty($strUser)){
				$this->error("你应该去火星生活啦！");
			}else{
				/* 调用注册接口注册用户 */
		        $User = new UserApi;
				$User->updateInfo($strUser['userid'], array('password'=>$pwd));
				$this->success('密码重新设置成功了；请登录！',C('ik_site_url'));
				
			}
		}else{
			$email = I('get.mail');
			$resetpwd = I('get.set');

			$userNum = $this->user_mod->where(array('email'=>$email,'resetpwd'=>$resetpwd))->find();
			
			if($email=='' || $resetpwd==''){
				$this->error("你应该去火星生活啦！");
			}elseif(empty($userNum)){
				$this->error("你应该去火星生活啦！");
			}
			
			$this->assign('email',$email);
			$this->assign('resetpwd',$resetpwd);
			
			$this->_config_seo ( array (
					'title' => '重设密码'
			) );
			$this->display();
		}
	}	
	
	// 用户等级
	public function role(){
		$arrRole = $this->role_mod->select();
		$this->assign('arrRole',$arrRole);

		$this->_config_seo ( array (
				'title' => '会员等级'
		) );
		$this->display();		
	}
	// 用户积分
	public function score(){
		$id = $this->_get('id','intval');
		$strUser = $this->user_mod->getOneUser($id);

		$score_log_mod = M('user_score_log');
		$map['uid'] = $id;
		$count = $score_log_mod->where($map)->count();
		$pager = $this->_pager($count, 20);
		$list = $score_log_mod->where($map)->order('id DESC')->limit($pager->firstRow.','.$pager->listRows)->select();
		$this->assign('list',$list);
		$this->assign('pageUrl', $pager->show());
	
		$this->_config_seo ( array (
				'title' => $strUser['username'].'的积分'
		) );
		$this->display();
	}

	/**
	 * 绑定旧用户 登录
	 */
	public function binduser( $email = '', $password = ''){
		if(cookie('user_bind_info')){
			$user = object_to_array(cookie('user_bind_info'));
			if(IS_POST){

				/* 调用UC登录接口登录 */
				$UserApic = new UserApi;
				$uid = $UserApic->login($email, $password);
				if(0 < $uid){ //UC登录成功
					/* 登录用户 */
					$Member = D('Common/User');
					if($Member->login($uid)){

						//开始执行绑定表
						$oauth = new \Common\Util\Oauth($user['type']);
						$bind_info = array(
								'ik_uid' => $strUser['userid'],
								'keyid' => $user['keyid'],
								'bind_info' => $user['bind_info'],
						);
						$oauth->bindByData($bind_info);
						//清理绑定COOKIE
						cookie('user_bind_info', NULL);
											
						//登录用户
						$this->redirect('space/index/index',array('id'=>$uid));
						
					} else {
						$this->error($Member->getError());
					}
	
				} else { //登录失败
					switch($uid) {
						case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
						case -2: $error = '密码错误！'; break;
						default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
					}
					$this->error($error);
				}				
			}			
		}else{
			$this->redirect('home/oauth/index',array('mod'=>'qq'));
		}		
	}
	/**
	 * 用户绑定 新增用户
	 */
	public function binding() {
		if(cookie('user_bind_info')){
			
			$user = object_to_array(cookie('user_bind_info'));
			if(IS_POST){
				$email    = $this->_post('email','trim');
				$username = $this->_post('username','trim');
				$password = I('post.password');
				$repassword = I('post.repassword');
				
				if(empty($email) || empty($username))
				{
					$this->error('Email和用户名称不能为空！');
				}
				if ($password != $repassword) {
					$this->error('密码和重复密码不一致！');
				}
				
				//检查email和用户名
				$ishave = $this->user_mod->where(array('email'=>$email))->count('*');
				if ($ishave>0) {
					$this->error('该Email已经被使用了！');
				}else{
					
					/* 调用注册接口注册用户 */
		            $Api = new UserApi;
					$uid = $Api->register($username, $password, $email);
					if(0 < $uid){ //注册成功
						
						//开始执行绑定表
						$oauth = new \Common\Util\Oauth($user['type']);
		                $bind_info = array(
		                    'ik_uid' => $uid,
		                    'keyid' => $user['keyid'],
		                    'bind_info' => $user['bind_info'],
		                );
		                $oauth->bindByData($bind_info);
		                //清理绑定COOKIE
		                cookie('user_bind_info', NULL);						
						
						// 登陆
						$uid = $Api->login($email, $password);
						if($uid > 0){
							/* 登录用户 */
							$Member = D('Common/User');
							if($Member->login($uid)){ //登录用户
								$this->redirect ( 'space/index/index', array ('id' => $uid ) );
							} else {
								$this->error($Member->getError());
							}							
						}

					} else { //注册失败，显示错误信息
						$this->error($this->showRegError($uid));
					} 

				}
			}
			$this->assign('user', $user);
			$this->_config_seo ( array (
					'title' => '完善信息',
					'subtitle' => '绑定帐号'
			) );
			$this->display();
		}else{
			$this->redirect('home/oauth/index',array('mod'=>'qq'));
		}

	}	
	
    /**
     * 活跃用户
     * @author 小麦 <810578553@qq.com>
     */
	public function activeusers(){
		//查询条件 是否审核
		$map['isenable'] = 0;
		//显示列表
		$pagesize = 65;
		$count = $this->user_mod->where($map)->order('last_login_time desc')->count('userid');
		$pager = $this->_pager($count, $pagesize);
		$arrItemid =  $this->user_mod->field('userid')->where($map)->order('last_login_time desc')->limit($pager->firstRow.','.$pager->listRows)->select();
		foreach($arrItemid as $key=>$item){
			$arrHotUser [] = $this->user_mod->getOneUser($item['userid']);
		}
		$this->assign('pageUrl', $pager->show());
		///////////////////////////////
		$this->assign('list', $arrHotUser);
		$this->_config_seo ( array (
				'title' => '活跃用户'
		) );
		$this->display();
	}
	
}