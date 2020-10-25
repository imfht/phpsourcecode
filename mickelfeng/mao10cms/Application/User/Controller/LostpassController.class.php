<?php
namespace User\Controller;
use Think\Controller;
class LostpassController extends Controller {
    public function index(){
        if(mc_user_id()) {
	        if(mc_is_mobile()) {
				        $this->success('登陆成功',U('user/index/pro?id='.mc_user_id()));
			        } else {
				        $this->success('登陆成功',U('user/index/index?id='.mc_user_id()));
			        }
        } else {
	        $this->theme(mc_option('theme'))->display('User/lostpass');
        }
    }
    public function submit(){
    	if($_POST['user_email'] && $_POST['user_pass']) :
	    	$page_id = M('meta')->where("meta_key='user_email' AND meta_value='".mc_magic_in($_POST['user_email'])."' AND type='user'")->getField('page_id');
	    	$pass = md5($_POST['user_pass'].mc_option('site_key'));
	    	mc_update_meta($page_id,'user_pass_lost',$pass,'user');
	    	$link = mc_option('site_url').'?m=user&c=lostpass&a=clink&id='.$page_id.'&pass='.$pass;
	    	$body = '请访问 '.$link.' 重置您的密码！';
	    	mc_mail($user_email,'重置密码',$body);
	    	$this->success('请登陆您的邮箱重置密码',U('user/login/index'),10);
    	else :
    		$this->error('必须填写完整的信息！');
    	endif;
    }
    public function clink() {
	    $id = M('meta')->where("page_id = '".mc_magic_in($_GET['id'])."' AND meta_key='user_pass_lost' AND meta_value='".mc_magic_in($_GET['pass'])."' AND type='user'")->getField('id');
	    if($id>0) {
	    	mc_update_meta(mc_magic_in($_GET['id']),'user_pass',mc_magic_in($_GET['pass']),'user');
		    $this->success('重置密码成功，请使用新密码登陆',U('user/login/index'));
	    } else {
		    $this->error('重置密码失败！');
	    }
    }
}