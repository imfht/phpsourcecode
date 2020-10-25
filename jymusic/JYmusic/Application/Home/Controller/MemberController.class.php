<?php
// +-------------------------------------------------------------+
// | Copyright (c) 2014-2015 JYmusic音乐管理系统                 |
// +-------------------------------------------------------------
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+

namespace Home\Controller;
use User\Api\UserApi;

/**
 * 用户控制器
 * 包括用户中心，用户登录及注册
 */
class MemberController extends HomeController {

	/* 用户中心首页 */
	public function index(){
		
	}

	/* 注册页面 */
	public function register($username = '', $password = '', $repassword = '', $email = '', $verify = ''){
        if(!C('USER_ALLOW_REGISTER')){
            $this->error('注册已关闭');
        }
		if(IS_POST){ //注册用户
			$str  = @explode(',',trim(C('REG_BAN_NAME')));
			if(count($str)>0){
				for( $i=0;$i<count($str);$i++){
	 				if( stristr($username,$str[$i])){
	 					$this->error('用户名['.$username.']禁止注册！');
	 				}
				}
			} 
			/* 检测验证码 */
			if(!check_verify($verify)){
				$this->error('验证码输入错误！');
			}					
			/* 检测密码 */
			if($password != $repassword){
				$this->error('密码和重复密码不一致！');
			}
			/* 调用注册接口注册用户 */
            $User = new UserApi;
			$uid = $User->register($username, $password, $email);
			if(0 < $uid){ //注册成功		
				//TODO: 发送通知
				if (C('REG_GREET_MSG')){
					$title = '感谢你注册'.C('WEB_SITE_NAME').'会员';
					$content = str_replace(array('{$webname}','{$webmail}'), array(C('WEB_SITE_NAME'),C('WEB_SITE_NAME')),C('REG_GREET_CONTENT'));
					$msg = D('Common/Message');						
					$msg->sendMsg($uid,$title,$content,$type='system');
				}		
				/*if (!C('SEND_REG_MAIL')){
					//发送注册成功提醒邮件
					$email = D('Mail')->send_register_email($uid);
				    //if (!$email)$this->error('测试邮件发送失败！');
				}*/
				$uid = $User->login($username, $password);
				D('Member')->login($uid); //登录用户*/
				$this->success('注册成功！',U('/'));
			} else { //注册失败，显示错误信息
				$this->error($this->showRegError($uid));
			}

		} else { //显示注册表单
			$this->display();
		}
	}

	/* 登录页面 */
	public function login($username = '', $password = '', $verify = '',$autologin = false){
		//dump($username);
		if(IS_POST){ //登录验证
			if(is_login()){
				$this->success('已经登录，请不要重复登录！');
			}			
			if(C('VERIFY_OFF')){// 检测验证码 
				if(!check_verify($verify)){
					$this->error('验证码输入错误！');
				}
			}			
			/* 调用UC登录接口登录 */
			$user = new UserApi;
			$uid = $user->login($username, $password);
			if(0 < $uid){ //UC登录成功
				/* 登录用户 */
				$status = D('Member')->login($uid);
				if($status){ //登录用户
					//判断是否自动登录
					if($autologin){
						$key = think_encrypt($uid,C('DATA_AUTH_KEY'));
						cookie('autologin',$key,30*24*3600); 
					}
					$url = Cookie('__forward__');
					$url = !empty($url)? $url : U('Index/index');
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

		} else { //显示登录表单
			$this->display();
		}
	}

	/* 退出登录 */
	public function logout(){
		if(is_login()){
			D('Member')->logout();
			cookie('autologin',null);
			$this->success('退出成功！', U('Index/index'));
		} else {
			$this->redirect('Member/login');
		}
	}

	/* 验证码，用于登录和注册 */
	public function verify(){
		$verify = new \Think\Verify();
		$verify->entry(1);
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
     * 修改密码提交
     * @author huajie <banhuajie@163.com>
     */
    public function profile(){
		if ( !is_login() ) {
			$this->error( '您还没有登陆',U('Member/login'));
		}
        if ( IS_POST ) {
            //获取参数
            $uid        =   is_login();
            $password   =   I('post.old');
            $repassword = I('post.repassword');
            $data['password'] = I('post.password');
            empty($password) && $this->error('请输入原密码');
            empty($data['password']) && $this->error('请输入新密码');
            empty($repassword) && $this->error('请输入确认密码');

            if($data['password'] !== $repassword){
                $this->error('您输入的新密码与确认密码不一致');
            }

            $Api = new UserApi();
            $res = $Api->updateInfo($uid, $password, $data);
            if($res['status']){
                $this->success('修改密码成功！');
            }else{
                $this->error($res['info']);
            }
        }else{
            $this->display();
        }
    }
        
    /*
    * 找回密码 需要邮箱插件
    */
    
    public function findpwd () {
    	if (is_login()) {
			$this->error( '您已登陆',U('Index/index'));
		}
    	if (IS_POST) {
    		$post = I('post.');
			/* 检测验证码 */
			if(!check_verify($post['verify'])){
				$this->error('验证码输入错误！');
			}
			/* 检测邮箱*/
			$email = filter_var($post['email'],FILTER_VALIDATE_EMAIL);
			if ($email){			
				$uid  =  M('ucenterMember')->getFieldByEmail($email,'id');
				if ($uid){
					$status  =  M('Member')->getFieldByUid($uid,'status');
					if ( 1 == $status ){
						R('Addons://Email/Email/renPassword',array($email));											
					}elseif (2 == $status) {
						$this->error('你的账号未激活,请到登录邮箱激活',U('Member/activate'));						
					}else{
						$this->error('你的账号已被禁用',U('Index/index'));	
					}						
				}else{
					$this->error('对不起，账号不存在！');
				}				
			}else{
				$this->error('邮箱格式不正确！');
			}
    		
    	}else{
      		$this->display();  			
    	}
    }
       
	/*	
	* ajax返回是否登录
	*/	
	public function getUser () {
		// 获取当前用户ID
        $id = is_login();
        $data['status']  = 0; 
       	if(!$id){// 还没登录
       		$userkey = cookie('autologin');
			if (!empty($userkey)){
		    	if ($uid = think_decrypt($userkey,C('DATA_AUTH_KEY')));
		    	$member = D('Member');    	
		    	$id = D('Member')->login($uid);
		    	$data['uid']  = $uid;
		    	$data['status']  = 1;
		    	$data['uid']  = $id;
        		$data['username']= get_nickname($id);       
			}                    
        }else {
        	$data['status']  = 1;
        	$data['uid']  = $id; 
        	$data['username']= get_nickname($id);       	
        }
        if (IS_POST && IS_AJAX) {
        	$this->ajaxReturn($data);	        		
        }elseif (IS_GET && IS_AJAX){
        	$this->assign('data',$data);	
        	$this->show(':Ajaxget/getTopUser');
        }
                       
	}
	
	/*
	*	用户注册协议
	*/
	
	public function accord () {
		$accord = C('REG_AGREE');	
		$content = str_replace(array('{$webname}','{$webemail}','{$webqq}','{$webphone}'),array(C('WEB_SITE_NAME'),C('WEB_EMAIL'),C('WEB_QQ'),C('WEB_PHONE')),$accord);
		if (IS_AJAX) {
			$data['status']  = 1;
			$data['content'] = $content;
			$this->ajaxReturn($data);
		}else{
			$this->assign('accord',$content);
			$this->display('register');
		}
	}
	
	/*
	* 用户激活
	*/	
	public function activate ($hash=null) {
		// 获取当前用户ID
		if (IS_POST) {
			$post = I('post.');
			/* 检测验证码 */
			if(!check_verify($post['verify'])){
				$this->error('验证码输入错误！');
			}
			/* 检测邮箱*/
			$email = filter_var($post['email'],FILTER_VALIDATE_EMAIL);
			if ($email){			
				$uid  =  M('ucenterMember')->getFieldByEmail($email,'id');
			}else{
				$this->error('邮箱格式不正确！');
			}
			if ($uid){
				$user = M('Member')->where(array('uid'=>$uid))->field('last_login_time,status')->find();
				$ltime = time()-$user['last_login_time'];				
				if($user['status'] != 2) {
					$this->error('该用户已经激活！',U('Member/login'));
				}elseif($ltime < 3600){//小于1小时禁止
					$this->error('请不要重复发送！');
				}else{
  					R('Addons://Email/Email/sendActivate',array($uid));		
				}
			}else{
				$this->error('对不起，账号不存在！');
			}			
		}elseif($hash){
			$hash = I('get.hash');
			$map['cdkey'] = $hash;
			$user  =  M('Member')->where($map)->field('uid,last_login_time,status')->find();			
			if($user['status'] != 2) {
				$this->error('该用户已经激活！',U('Member/login'));
			}else{
				$ltime = time()-$user['last_login_time'];
				if ($ltime > 86400){//大于24小时
					$this->assign('info','激活链接已失效');	
					$this->display('login');
				}else{
					M('Member')->where($map)->setField('status',1);
					$this->success('邮箱激活成功', U('Member/login'));
					if (C('SEND_REG_MAIL')){
  						R('Addons://Email/Email/sendRegister',array($user['uid']));
  					}
				}			
			}
		}else{
			$this->assign('info','如果你未收到激活邮件或链接已失效');	
			$this->display('login');
		}
	}
	

	public function getmsg(){
		if ( $uid = is_login()) {
			$msg = D('Common/Message');
			$list = $msg->getUnreadMsg($uid);
	        if ( IS_GET && IS_AJAX) {	//ajax	 get 渲染模板 返回html	
				$this->assign('list',$list);	
				$this->show(':Ajaxget/getTopMsg');
			}elseif(IS_POST && IS_AJAX){//ajax	 post  返回json
				$data['unread']  = count($list);
				$data['info'] = $list;				
				$this->ajaxReturn($data);			
			}
		}
	}
}
