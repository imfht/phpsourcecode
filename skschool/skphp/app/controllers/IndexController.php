<?php
namespace App\Controllers;
use Skschool\Controller;
use Skschool\Config;

class IndexController extends Controller {
	
	public function index()
	{
		$this->assign('name', 'IndexController@index');
		$this->display();
	}
	
	public function cache(){
		/**
		 * WC 写入读取数据缓存
		 * @param  string	$name		缓存名
		 * @param  mixed	$value		缓存数据
		 * @return
		 */

		// 写入缓存
		WC('test', 'this is data');

		// 获取缓存
		echo WC('test');

		// 清空缓存
		// WC('test', '');
		
	}
	
	public function config(){
		/**
		 * use Skschool\Config;
		 */
		
		// 获取配置文件
		echo Config::get('error_page');
		

		// 获取全部配置文件
		/* $list = Config::all();
		p($list); */
		
		// 动态修改配置文件  增加  || 修改
		// $status = Config::set('error_page', 'abc.com');
		// echo '修改配置文件状态： '.$status;
	}
	
}