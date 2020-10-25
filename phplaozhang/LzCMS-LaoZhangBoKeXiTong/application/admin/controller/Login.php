<?php
namespace app\admin\controller;

/**
* admin公共类
*/
class Login extends Init
{
	
	function _initialize()
	{
		parent::_initialize();
	}

	public function login()
	{
		if(request()->isPost()){
			$params = input('post.');
			if(!captcha_check($params['captcha'])){
				return json(array('code'=>0,'msg'=>'验证码不正确'));
			};
			$result = model('admin')->do_login($params);
			if($result){
				return json(array('code'=>200,'msg'=>'登陆成功'));
			}else{
				return json(array('code'=>0,'msg'=>'用户名或者密码不正确'));
			}
		}
		if(session('?admin_user')){
			$this->redirect('index/index');
		}
		return view('public/login');
	}

	public function logout()
	{
		session(null);
		if(!session('?admin_user')){
			return json(array('code'=>200,'msg'=>'退出成功'));
		}else{
			return json(array('code'=>0,'msg'=>'退出失败'));
		}
	}

	
}