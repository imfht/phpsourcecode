<?php 
namespace app\admin\controller;
use think\Controller;
use think\captcha\Captcha;

/**.-------------------------------------------------------------------
 * |    Software: [QeyserBlog]
 * |    Site: www.qeyser.net
 * |-------------------------------------------------------------------
 * |    Author: 凯萨尔 <125790757@qq.com>
 * |    WeChat: 15999230034
 * |    Copyright (c) 2017-2018, www.qeyser.net . All Rights Reserved.
 * '-------------------------------------------------------------------*/

 class Login extends Controller {
 	/**
 	 * 登陆
 	 */
 	public function login(){
 		if(request()->isPost()){
 			$data =[
 				'username'=>input('isim'),
 				'password'=>input('parol'),
 				'verify'=>input('verify')
 			];
            //检测验证码
			if(!captcha_check($data['verify'])){
				$this->error("تەستىق نۇمۇرىنى توغرا كىرگۈزۈڭ !");
			};
			//登录判断
			$res = db('admin')->where('username','=',$data['username'])->find();
			if($res && $res['password'] === md5($data['password'])){
				Session('user_name',$res['username']); Session('user_id',$res['uid']); Session('user_salt','#2017'.$res['salt'].time());
				$this->success('كىرىش مۇۋاپىقيەتلىك بولدى !','admin/index/index');
			}else{
				$this->error('ئەزا نامى ياكى مەخپىي نۇمۇر خاتا !');
			}
 			return;
 		}
 		return $this->fetch(); // onclick="this.src='{:captcha_src()}?'+Math.random();"
 	}
 }