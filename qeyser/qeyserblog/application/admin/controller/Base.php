<?php
namespace app\admin\controller;
use think\Controller;

/**.-------------------------------------------------------------------
 * |    Software: [QeyserBlog]
 * |    Site: www.qeyser.net
 * |-------------------------------------------------------------------
 * |    Author: 凯撒 <125790757@qq.com>
 * |    WeChat: 15999230034
 * |    Copyright (c) 2017-2018, www.qeyser.net . All Rights Reserved.
 * '-------------------------------------------------------------------*/

class Base extends Controller{
	public function _initialize(){
		/* 检测是否已安装 */
		if (!is_file(APP_PATH . 'database.php') || !is_file(APP_PATH . 'install.lock')) {
			return $this->redirect('install/index/index');
		}
		/* 判断session是否存在 */
		if(!Session('user_id') && !Session('user_name') && !Session('user_salt')){
			$this->error('سېستىمىغا كىرىپ ئاندىن مەشغۇلات قىلىڭ!','admin/login/login');
		}
		/* 判断是否被闯入 */
		$result = db('admin')->where('uid',Session('user_id'))->value('salt');
		if($result != msubstr(Session('user_salt'),5,12,"utf-8",false)){
			Session('user_id',null);Session('user_name',null);Session('user_salt',null);
			$this->error('سېستىمىغا كىرىپ ئاندىن مەشغۇلات قىلىڭ!','admin/login/login');
		}
	}
	/**
	 * 退出登陆
	 */
	public function logout(){
		Session('user_id',null);
		Session('user_name',null);
		Session('user_salt',null);
		$this->success('مۇۋاپىقيەتلىك چېكىندىڭىز ~','admin/login/login');
	}

}