<?php
/*
 * 主页类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class Index extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');

	}	
		
	public function main(){
		$menu_arr = _instance('Action/Auth')->auth_menu_tree_arr();
		$sysinfo  = $this->L('Sys')->get_sys_info();
		
		if(empty($serial)){
			$sysinfo=array_merge($sysinfo,$this->L('Auth')->sys_default_conf());
		}
		$smarty   = $this->setSmarty();
		$smarty->assign(array("menu"=>$menu_arr,'sys'=>$sysinfo));//框架变量注入同样适用于smarty的assign方法
		$smarty->display('index.html');	
	}
	
	//得到系统配置参数
	public function get_sys_config(){
		$sql 	= "select * from fly_sys_config;";
		$list	= $this->C($this->cacheDir)->findAll($sql);
		
		if(is_array($list)){
			foreach($list as $key=>$row){
				$assArr[$row["name"]] = $row["value"];
			}
		}
		return $assArr;		
	}
	
	
	public function sys_menu(){
		$smarty  = $this->setSmarty();
		//$smarty->assign($article);//框架变量注入同样适用于smarty的assign方法
		$smarty->display('sys_menu.html');	
	}
	public function xx(){
		$smarty  = $this->setSmarty();
		//$smarty->assign($article);//框架变量注入同样适用于smarty的assign方法
		$smarty->display('demo_page6.html');		
	}
	
	
	
//this=>assign方法有如下方式

//$this->assign('A',$A);
//$this->assign(array('A'=>$A,'B'=>$B,'E'=>$E,'F'=>$F));
//$this->assign(array('A'=>$A,'B'=>$B),array('E'=>$E,'F'=>$F));
	public function cacheTest(){
	
		$article=_instance('Action/Article')->noCacheArticle();
		//实例化Action下的Article类并调用其execute方法，返回数组值。
		$this->assign($article);
		$this->show('index');	
		//参数留空则默认访问View下的index.php,若在View的子目下，可以使用$this->show('test/index'); 
	}
 
}//
?>