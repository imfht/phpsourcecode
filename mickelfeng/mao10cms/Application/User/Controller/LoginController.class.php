<?php
namespace User\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function index(){
        if(mc_user_id()) {
	        $this->success('您已经登陆',U('user/index/edit?id='.mc_user_id()));
        } else {
			$this->theme(mc_option('theme'))->display('User/login');
        }
    }
    public function submit(){
        $ip_false = M('option')->where("meta_key='ip_false' AND type='user'")->getField('meta_value',true);
        if($ip_false && in_array(mc_user_ip(), $ip_false)) {
        	$this->error('您的IP被永久禁止登陆！');
        } else {
	        $page_id = M('meta')->where("meta_key='user_name' AND meta_value='".mc_magic_in(I('param.user_name'))."' AND type='user'")->getField('page_id');
	        $user_pass_true = mc_get_meta($page_id,'user_pass',true,'user');
	        if($_POST['user_name'] && $_POST['user_pass'] && md5($_POST['user_pass'].mc_option('site_key')) == $user_pass_true) {
		        $user_pass = md5(I('param.user_pass').mc_option('site_key'));
		        cookie('user_name',I('param.user_name'),36000000000);
		        cookie('user_pass',$user_pass,36000000000);
		        $ip_array = M('action')->where("page_id='".mc_user_id()."' AND action_key='ip'")->getField('action_value',true);
		        if($ip_array && in_array(mc_user_ip(), $ip_array)) {
			        
		        } else {
			        if(!mc_is_admin()) {
				        mc_add_action(mc_user_id(),'ip',mc_user_ip());
			        };
		        };
		        if($_POST['comefrom']) {
			        $this->success('登陆成功',$_POST['comefrom']);
		        } else {
			        if(mc_is_mobile()) {
				        $this->success('登陆成功',U('user/index/pro?id='.mc_user_id()));
			        } else {
				        $this->success('登陆成功',U('user/index/index?id='.mc_user_id()));
			        }
		        }
	        } else {
	        	$this->error('用户名与密码不符！');
	        };
        };
    }
    public function logout(){
        cookie('user_pass','user_pass',1);
        $this->success('您已经成功退出登陆',U('home/index/index'));
    }
    public function connect_qq(){
    	require_once THINK_PATH.'../connect-qq/oauth/callback.php';
    }
    public function connect_weibo(){
    	require_once THINK_PATH.'../connect-weibo/callback/callback.php';
    }
}