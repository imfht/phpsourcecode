<?php
namespace app\index\controller;
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

	public function _initialize() {
		/* 检测是否已安装 */
		if (!is_file(APP_PATH . 'database.php') || !is_file(APP_PATH . 'install.lock')) {
			return $this->redirect('/index.php/install');
		}
	}

	// 显示tag列表
	public function tags(){
		$tags = db('article')->where('keywords','neq','')->column('keywords');
		static $arr = array();
		foreach ($tags as $k => $v) {
			$tag = $v;
			$arrs = explode('،', $tag);
			$arr = array_merge($arrs,$arr);
		}
		if($arr){
        	$res = array_unique($arr);            
        }else{
        	$res = 'No tags !';
        }
		$this->assign('res',$res);
	}

	public function freandlink(){
		// 显示友情链接列表
		$link = db('links')->order('sort asc')->select();
		$this->assign('link',$link);
	}
}