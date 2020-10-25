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

class organizationService extends organization implements _organization{

	public static $_instance; //用于单例模式
	
	//实例化(单例模式)
    public static function getInstance(){
        if(null === self::$_instance) self::$_instance = new self();
        return self::$_instance;
    }

	//获取组织详情
	static function getInfo($organizationid = 0){
		$id = (int)$organizationid;
		if($id < 1) return false;
		self::getInstance();
		$info = self::$_instance->getOne($id);
		$return = $info;
		return $return;
	}
	
	//获取组织列表
	static function doList($sql = '', $page = 1, $pagesize = PAGE_SIZE){
		//$sql where 里面的东西
		$offset = ($page - 1) * $pagesize;
		self::getInstance();
		$return = self::$_instance->getList($sql, '', $offset, $pagesize);
		return $return;
	}

	//添加组织
	static function doAdd($organizationArray = ''){
		self::getInstance();
		$time = time();
		$organizationid = self::$_instance->add($organizationArray);		
		return $organizationid;
	}

	//更新组织
	static function doUpdate($organizationid = 0,$organizationArray = ''){
		if($organizationid < 1) return false;
		self::getInstance();
		$organizationid = self::$_instance->editBy($organizationArray, "organizationid=".$organizationid);
		return $organizationid;	
	}

	//获取组织总数
	static function doCount($sql = ''){
		self::getInstance();
		$return = self::$_instance->getCount($sql);
		return $return;
	}

	//获取分组详情
	static function getGroupInfo(){
		return $return;
	
	}

	//添加分组
	static function addGroup(){
		return $return;
	
	}

	//更新分组
	static function updateGroup(){
		return $return;
	
	}

	//获取分组列表
	static function getGroupList(){
		return $return;
	
	}

	//获取角色列表
	static function getRoleList(){
		$_aclrole = new aclrole();
		$return = $_aclrole->getList();
		return $return;
	}

}

?>