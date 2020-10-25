<?php
/**
 * oscshop 电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace Home\Controller;

class PublicController extends CommonController {
	
	function forgot(){
		if(IS_POST){
			$json=array();
			
			$d=I('post.');
			
			if (empty($d['email'])) {
				$json['error']['email'] = '邮箱必填！！';
			}elseif(!(strlen($d['email']) > 6 && preg_match("/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/", $d['email']))){
				$json['error']['email'] = '请输入正确的邮箱！！';
			}elseif(!M('member')->where(array('email'=>$d['email']))->find()){
				$json['error']['email'] = '系统不存在该邮箱！！';
			}
			
			if($json){
				$this->ajaxReturn($json);
				die;			
			}
			
			$rand_num=rand(100000,999999);
			
			$account['pwd']=think_ucenter_encrypt($rand_num,C('PWD_KEY'));			
			
			$r=M('member')->where(array('email'=>$d['email']))->save($account);
			
			if($r){
				
				$user_info=M('member')->where(array('email'=>$d['email']))->find();
				
				storage_user_action($user_info['member_id'],$user_info['uname'],C('FRONTEND_USER'),'使用邮件重置了密码');
				
				$email_content='您好,您的密码已经重置成功<br />'.
				'您的账号是 '.$user_info['uname'].'<br />'.
				'邮箱是 '.$user_info['email'].'<br />'.
				'密码是 '.$rand_num.'<br />'.
				'您可以使用账号或者邮箱来进行网站的登录<a href="'.C('SITE_URL').'/login.html">点此进行登录</a>';
				
				//发送邮件
				think_send_mail($d['email'],$rand_num,'密码重置成功-'.C('SITE_NAME'),$email_content);
								
				$json['redirect'] = U('/login');
				$this->ajaxReturn($json);
				die;
			}
			
		}
		if(is_login()){
			$this->redirect('/order');
		}
		$this->action=U('/forgot');
		$this->display();
	}
	
	
	/* 注册页面 */
	public function register(){
	
		if(IS_POST){
			$this->uname=I('uname');
			$this->email=I('email');
			$this->pwd=I('pwd');
			$this->repwd=I('repwd');
			
			if(!check_verify(I('code'))){
	            $this->error='验证码输入错误！';
				$this->display();die();
				
	        }			
			
			if(empty($_POST['uname'])){
				$this->error_uname="用户名不能为空！！";				
				$this->display();die();
			}elseif(M('Member')->getByUname(trim($_POST['uname']))){
				$this->error_uname="用户名已经存在！！";
				$this->display();die();
			}
			elseif(empty($_POST['email'])){
				$this->error_email="邮箱不能为空！！";
				$this->display();die();
			}elseif(M('Member')->getByEmail($_POST['email'])){
				$this->error_email="邮箱已经存在！！";
				$this->display();die();
			}elseif(empty($_POST['pwd'])){
				$this->error_pwd="密码不能为空！！";
				$this->display();die();
			}elseif(empty($_POST['repwd'])){
				$this->error_repwd="确认密码不能为空！！";
				$this->display();die();
			}elseif($_POST['pwd']!=$_POST['repwd']){
				$this->error_repwd="两次密码输入不相等！！";
				$this->display();die();
			}
			
			$data = array();
	       
		   	$data['email']=$_POST['email'];
			$data['uname']=trim($_POST['uname']);
			$data['pwd']  =think_ucenter_encrypt($_POST['pwd'],C('PWD_KEY'));
		    $data['status']=1;
	        $data['create_time']	=	time();				       
			$data['last_login_ip']	=	get_client_ip();
			
	       $re= M('Member')->add($data);
		   if($re){
		   		$auth = array(
		            'uid'             => $re,
		            'username'        => $data['uname'],		      
				 );	
		   		session('user_auth', $auth);
	    		session('user_auth_sign', data_auth_sign($auth));	
				
				storage_user_action($re,$data['uname'],C('FRONTEND_USER'),'注册成为会员');
				
				$email_content='您好,感谢您注册成为'.C('SITE_NAME').'会员<br />'.
				'您的账号是 '.$data['uname'].'<br />'.
				'邮箱是 '.$data['email'].'<br />'.
				'密码是 '.$_POST['pwd'].'<br />'.
				'您可以使用账号或者邮箱来进行网站的登录<a href="'.C('SITE_URL').'/login.html">点此进行登录</a>';
				
				//发送邮件
				think_send_mail($data['email'],$data['uname'],C('SITE_NAME').'会员注册成功',$email_content);
				
				$this->redirect('/login');	
		   }else{
		   		$this->error="注册失败";
				$this->display();die();
		   }
		   		
		}
		/**/
		$this->title='用户注册-';
		$this->meta_keywords=C('SITE_KEYWORDS');
		$this->meta_description=C('SITE_DESCRIPTION');		
		
        $this->display();

	}

	/* 登录页面 */
	public function login(){

		if(IS_POST){
			
			if(!check_verify(I('code'))){
	            $this->error='验证码输入错误！';
				$this->display();
				die();			
	        }
			    		
			if(empty($_POST['uname'])){
				$this->error="用户名 / email不能为空!!";
				$this->display();die();
			}elseif(empty($_POST['pwd'])){
				$this->error="密码不能为空!!";
				$this->display();die();
			}
			$user=M('Member')->getByUname($_POST['uname']);	
			if(!$user){
				$user=M('Member')->getByEmail($_POST['uname']);	
			}
			//用户存在且可用
			if($user&&$user['status']==1){			
				//验证密码
				if(think_ucenter_encrypt($_POST['pwd'],C('PWD_KEY'))==$user['pwd']){
					
			        $auth = array(
			            'uid'             => $user['member_id'],
			            'username'        => $user['uname'],		     
			            'status'		  => $user['status'] 
					 );			
					 	
				    session('user_auth', $auth);
		    		session('user_auth_sign', data_auth_sign($auth));					
				
					if($user['address_id']!=0){
						session('shipping_address_id',$user['address_id']);
					}
					storage_user_action($user['member_id'],$user['uname'],C('FRONTEND_USER'),'登录了网站');
					
			        $data = array();
			        $data['member_id']	=	$user['member_id'];
		
			        $data['last_login_time']	=	time();				
			        $data['login_count']		=	array('exp','login_count+1');
					$data['last_login_ip']	=	get_client_ip();
					$tip=new \Lib\Taobaoip();
					$ip_region=$tip->getLocation($data['last_login_ip']);
					
					$data['last_ip_region']=$ip_region['region'].'-'.$ip_region['city'];
					
			        M('Member')->save($data);
					
					$this->redirect('/order');
					
				}else{
					$this->error='密码错误！！';
					$this->display();die();
				}
			}else{
				$this->error="用户不存在或被禁用！！";
				$this->display();die();
			}				
	
	        } else {
	        		
				$this->title='用户登录-';
				$this->meta_keywords=C('SITE_KEYWORDS');
				$this->meta_description=C('SITE_DESCRIPTION');	
	        	
	            if(is_login()){
	                $this->redirect('/order');
	            }else{			
	                $this->display();
	            }
        }
	}

	/* 退出登录 */
	public function logout(){
               	    
		session('[destroy]');
     	$this->redirect('/login');
        
	}

    public function verify(){
        $verify = new \Think\Verify();
		$verify->codeSet = '2345689'; 
		$verify->fontSize = 30;
		$verify->length   = 4;
		$verify->useCurve = false;
		$verify->useNoise = true;
        $verify->entry(1);
    }





}
