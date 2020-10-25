<?php
namespace addons\common\coupon\controller;

use think\addons\Controller;

class Base extends Controller
{

	public function _initialize(){
         if (request()->isOptions()){
            
            abort(json(true,200));
        }
	    // 判断是否登录，没有登录跳转登录页面
		if(!session('user_auth') || !session('user_auth_sign')){
			$this->redirect('admin/public/login');
		}
		//资源加载
    	if(strstr(request()->baseFile(), 'public')){
            $this->view->replace([
                '__PUBLIC__'       =>  request()->root(true),
                '__CSS__'       =>  request()->root(true).'/../addons/common/coupon/view/public/css',
                '__IMG__'       =>  request()->root(true).'/../addons/common/coupon/view/public/img',
            ]);
        }else{
            $this->view->replace([
                '__PUBLIC__'       =>  request()->root(true).'/public',
                '__CSS__'       =>  request()->root(true).'/addons/common/coupon/view/public/css',
                '__JS__'       =>  request()->root(true).'/addons/common/coupon/view/public/js',
                '__IMG__'       =>  request()->root(true).'/addons/common/coupon/view/public/img',
            ]);
        }
		if ($this->request->isPjax()){
			$this->view->engine->layout(false);
		}else{
			$this->view->engine->layout('./application/admin/view/layout_addons.html');
		}
	}
}