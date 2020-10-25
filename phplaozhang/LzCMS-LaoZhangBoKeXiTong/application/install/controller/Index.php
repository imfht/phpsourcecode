<?php
namespace app\install\controller;

use think\Controller;
/**
* 安装控制器类
*/
require_once(APP_PATH.'install/library/checkconfig.php');
class Index extends Controller
{
	public function _initialize() {
		parent::_initialize();
		if(file_exists(APP_PATH.'install.lock')){
			header("Content-type:text/html;charset=utf-8");
			die('您已安装过LzCMS-博客版，请勿重复执行安装操作！');
		}
	}
	public function index(){
		
		return view('index');
	}
	public function setup1(){
		return view('setup1');
	}
	public function setup2(){
		return view('setup2');
	}
	public function setup3(){
		return view('setup3');
	}
	public function setup4(){
		//生成lock文件
		$is_success = file_put_contents(APP_PATH.'install.lock','LzCMS-博客版:'.date('Y-m-d H:i:s').' by '.LZ_VERSION);
		if(!$is_success)
		{
			die('create install.lock file fail');
		}
		return view('setup4');
	}
	public function ajax_check_mysql(){
		check_mysql();
	}
	public function clear_cache(){
		//更新缓存
		$result = model('common/cache')->update_cache();
	}

}