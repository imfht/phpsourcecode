<?php
namespace addons\shuiguo\delivery\controller;

use think\addons\Controller;

class Base extends Controller
{
	public function _initialize(){
		// 判断是否登录，没有登录跳转登录页面
		if(!session('user_auth') || !session('user_auth_sign')){
			$this->redirect('admin/public/login');
		}
		//资源加载
    	if(strstr(request()->baseFile(), 'public')){
            $this->view->replace([
                '__PUBLIC__'       =>  request()->root(true),
            ]);
        }else{
            $this->view->replace([
                '__PUBLIC__'       =>  request()->root(true).'/public',
            ]);
        }
		if ($this->request->isPjax()){
			$this->view->engine->layout(false);
		}else{
			$this->view->engine->layout('./application/admin/view/layout_addons.html');
		}
	}

}