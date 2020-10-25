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

class groupService extends group implements _group{

	public static $_instance; //用于单例模式
	
	//实例化(单例模式)
    public static function getInstance(){
        if(null === self::$_instance) self::$_instance = new self();
        return self::$_instance;
    }
	
	//获取列表
	
	
	//列表
	static function doList($sql = '', $page = 1, $pagesize = PAGE_SIZE){
		//$sql where 里面的东西
		$offset = ($page - 1) * $pagesize;
		self::getInstance();
		$return = self::$_instance->getList('', '', $offset, $pagesize);
		foreach($return as $key=>$value){
			$group=groupService::getInfo($value['groupid']);
			$return[$key]['name']=  $group['name'] ;
		}
		return $return;
	}


	//获取详情
	static function getInfo($groupId = 0){
		if($groupId < 1) return false;
		self::getInstance();
		$info = self::$_instance->getOne($groupId);
		$return = $info;
		return $return;
	}

	//添加
	static function doAdd($groupArray=''){
		self::getInstance();
		$time = time();
		$groupArray['createtime']=date('Y-m-d H:i:s',$time);
		$groupId = self::$_instance->add($groupArray);
		
		return $groupId;
	}

	//更新
	static function doUpdate($groupId=0,$groupArray=''){
		if($groupId < 1) return false;
		self::getInstance();
		$groupId = self::$_instance->editById($groupId,$groupArray);
		return $groupId;
	}

	//删除
	static function doDelete($groupId=0){
		if($groupId < 1) return false;
		self::getInstance();
		$return = self::$_instance->delete($groupId);
		return $groupId;
	}

	//更新状态
	static function updateStatus($groupid = 0,$status = 1){
		if($groupid < 1) return false;
		self::getInstance();
		$data = array();
		$data['status'] = $status;
		$return = self::$_instance->editById($groupid , $data);
		return $return;
	}

}

?>