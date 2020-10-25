<?php

namespace Common\Controller;

use Think\Controller;

class CommonController extends Controller {

	public function _initialize()
    {

        //记住登陆
 		D('Common/Member')->rembember_login();
 		//获取站点LOGO
 		$logo = get_cover(modC('LOGO',0,'Config'),'path');
        $logo = $logo?$logo:__ROOT__.'/Public/images/logo.png';
        $this->assign('logo',$logo);
        //获取用户基本资料
		$common_header_user = query_user(array('nickname','avatar32'));
		$this->assign('common_header_user',$common_header_user);

		//获取用户菜单
		$user_nav=S('common_user_nav');
        if($user_nav===false){
        	$user_nav=D('UserNav')->order('sort asc')->where('status=1')->select();
        	S('common_user_nav',$user_nav);
        }
        $this->assign('user_nav',$user_nav);
        //邀请注册开关
        $register_type=modC('REGISTER_TYPE','normal','Invite');
        $register_type=explode(',',$register_type);
        $invite = in_array('invite',$register_type);
        $this->assign('invite',$invite);
        //用户登录、注册
        $open_quick_login=modC('OPEN_QUICK_LOGIN', 0, 'USERCONFIG');
        $this->assign('open_quick_login',$open_quick_login);
        $only_open_register=0;
        if(in_array('invite',$register_type)&&!in_array('normal',$register_type)){
         	$only_open_register=1;
        }
        $this->assign('only_open_register',$only_open_register);
        $login_url = U('Ucenter/Member/login');
        $this->assign('login_url',$login_url);
    }
}