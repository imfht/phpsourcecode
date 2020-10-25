<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 服务
*/

defined('INPOP') or exit('Access Denied');

include_once("config.php"); //加载配置

class regService extends reg implements _reg{

	public static $_instance; //用于单例模式
	
	//实例化(单例模式)
    public static function getInstance(){
        if(null === self::$_instance) self::$_instance = new self();
        return self::$_instance;
    }

	//获取注册详情
	static function getInfo($mnsid = 0){
		$id = (int)$mnsid;
		if($id < 1) return false;
		self::getInstance();
		$info = self::$_instance->getOne($id);
		$return = $info;
		return $return;
	}
	
	//获取注册列表
	static function doList($sql = '', $page = 1, $pagesize = PAGE_SIZE){
		//$sql where 里面的东西
		$offset = ($page - 1) * $pagesize;
		self::getInstance();
		$return = self::$_instance->getList($sql, '', $offset, $pagesize);
		return $return;
	}

	//添加注册服务
	static function doAdd($mnsArray = ''){
		self::getInstance();
		$time = time();
		$mnsid = self::$_instance->add($mnsArray);		
		return $mnsid;
	}

	//更新注册服务
	static function doUpdate($mnsid = 0,$mnsArray = ''){
		if($mnsid < 1) return false;
		self::getInstance();
		$mnsid = self::$_instance->editBy($mnsArray, "mnsid=".$mnsid);
		return $mnsid;	
	}

	//获取注册服务总数
	static function doCount(){
		return true;
	}
}

?>