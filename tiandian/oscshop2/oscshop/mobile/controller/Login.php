<?php
/**
 * oscshop2 B2C电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015-2016 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
 
namespace osc\mobile\controller;
use osc\common\validate\Member;
use think\Db;
use think\captcha\Captcha;
class Login extends MobileBase{
	
	function logout(){
		cookie('mobile_user_info',null);	
		session('mobile_total',null);
		$this->redirect('/mobile');		
	}
	
	function refresh_member($user){
		
		if(empty($user)&&!is_array($user)){
			return;	
		}			
		cookie('mobile_user_info',$user);		
	}
	
	//登录验证
	public function validate_login($data){
		
			if(empty($data['username'])){
				return ['error'=>'用户名必填'];
			}elseif(empty($data['password'])){
				return ['error'=>'密码必填'];
			}
			if(1==config('use_captcha')){				
				if(!check_verify($data['captcha'])){
					return ['error'=>'验证码错误'];
				}
			}
			$user=Db::name('member')->where('username',$data['username'])->find();
			
			if(!$user){
				return ['error'=>'账号不存在'];
			}elseif(($user['checked']==0)&&(1==config('reg_check'))){//需要审核
				return ['error'=>'该账号未审核通过'];
			}
			
			if(think_ucenter_encrypt($data['password'],config('PWD_KEY'))==$user['password']){
		
				$auth = array(
		            'uid'             => $user['uid'],
		            'username'        => $user['username'],
		            'nickname'        => $user['nickname'],
		            'group_id'		  => $user['groupid'],		                     
				 );			
								
				$this->refresh_member($auth);
				
				$login['lastdate']=time();
				$login['loginnum']		=	Db::raw('loginnum+1');
				$login['lastip']	=	get_client_ip();
				
				DB::name('member')->where('uid',$user['uid'])->update($login);
				
				storage_user_action($user['uid'],$user['nickname'],config('FRONTEND_USER'),'登录了网站');
				
				return ['success'=>'登录成功','total'=>osc_cart()->count_cart_total($user['uid'])];
			}else{
				return ['error'=>'密码错误'];
			}
	}
	
 	function login(){
	
		if(request()->isPost()){
			$data=input('post.');	
			
			$r=$this->validate_login($data);
			
			if(isset($r['error'])){
				return $r;
			}elseif($r['success']){
				osc_service('mobile','user')->set_cart_total($r['total']);			
				return ['success'=>'登录成功','url'=>cookie('jump_url')];
			}
		}
		$this->assign('SEO',['title'=>'登录-'.config('SITE_TITLE')]);
		$this->assign('top_title','登录');
        return $this->fetch();
    }
	function reg(){
	
		if(request()->isPost()){
			
			$data=input('post.');					
			 
			if(1==config('use_captcha')){				
				if(!check_verify($data['captcha'])){
					return ['error'=>'验证码错误'];
				}
			}  
			 	
			$validate=new Member();
				
			if(!$validate->check($data)){				
			    return ['error'=>$validate->getError()];				
			}
			
			$member['username']=$data['username'];
			$member['reg_type']='mobile';
			$member['password']=think_ucenter_encrypt($data['password'],config('PWD_KEY'));
			$member['groupid']=config('default_group_id');
			
			$member['regdate']=time();
			$member['lastdate']=time();			
			
			$member['nickname']=$data['username'];
			
			
			if(1==config('reg_check')){//需要审核或者验证
				$member['checked']=0;
			}else{
				$member['checked']=1;
			}
			
			$uid=Db::name('member')->insert($member,false,true);
			
			if($uid){
				
				//写入用户权限表
				Db::name('member_auth_group_access')->insert(['uid'=>$uid,'group_id'=>$member['groupid']],false,true);				 		
				
				if(1==config('reg_check')){//需要审核
					return ['success'=>'注册成功，请等待管理员审核','check'=>1,'url'=>cookie('jump_url')];
				}else{//不需要审核
					$auth = array(
		            'uid'             => $uid,
		            'username'        => $member['username'],		           
		            'group_id'		  => $member['groupid']		          	            
					 );	
					 
					$this->refresh_member($auth);
					
					storage_user_action($uid,$member['username'],config('FRONTEND_USER'),'注册成为会员');
					
					return ['success'=>'注册成功','check'=>0,'url'=>cookie('jump_url')];
				}
				
			}else{
				return ['error'=>'注册失败'];
			}
			
		}
		$this->assign('SEO',['title'=>'注册-'.config('SITE_TITLE')]);
		$this->assign('top_title','注册');
        return $this->fetch();
    }

 	public function verify(){	 	
		$captcha = new Captcha((array)Config('captcha'));
		return $captcha->entry(1);	 	
    }

}
