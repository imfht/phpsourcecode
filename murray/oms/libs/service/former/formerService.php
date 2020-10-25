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

class formerService extends former implements _former{

	public static $_instance; //用于单例模式
	
	//实例化(单例模式)
    public static function getInstance(){
        if(null === self::$_instance) self::$_instance = new self();
        return self::$_instance;
    }

	//获取模型详情
	static function getInfo($formerId = 0){
		if($formerId < 1) return false;
		self::getInstance();
		$info = self::$_instance->getOne($formerId);
		$return = $info;
		return $return;
	}

	//添加模型
	static function doAdd($formerArray=''){
		self::getInstance();
		$time = time();
		$fieldArray['uid'] = 1;
		//$formerArray['createtime']=date('Y-m-d H:i:s', $time);
		$formerId = self::$_instance->add($formerArray);		
		return $formerId;
	}

	//更新模型
	static function doUpdate($formerId = 0,$formerArray = ''){
		if($formerId < 1) return false;
		self::getInstance();
		$formerId = self::$_instance->editBy($formerArray, "formerid=".$formerId);
		return $formerId;
	}

	//更新模型状态
	static function updateStatus($formerId = 0,$status = 1){
		if($formerId < 1) return false;
		self::getInstance();
		$data = array();
		$data['status'] = $status;
		$return = self::$_instance->editById($formerId, $data);
		return $return;
	}
	
	//获取模型列表
	static function doList($sql = '', $page = 1, $pagesize = PAGE_SIZE){
		//$sql where 里面的东西
		self::getInstance();
		$offset = ($page - 1) * $pagesize;
		$return = self::$_instance->getList($sql, '', $offset, $pagesize);
		return $return;
	}

	//删除模型
	static function doDelete($formerId=0){
		if($formerId < 1) return false;
		self::getInstance();
		$return = self::$_instance->delete($formerId);
		return $formerId;
	}

	//获取模型总数
	static function doCount($sql = ''){
		self::getInstance();
		$return = self::$_instance->getCount($sql);
		return $return;
	}

	//获取原型详情
	static function getPrototypeInfo($prototypeid = 0){
		$id = (int)$prototypeid;
		if($id < 1) return false;
		$_prototype = new prototype();
		$info = $_prototype->getOne($id);
		$return = $info;
		return $return;
	}

	//添加原型
	static function addPrototype($prototypeArray=''){
		$_prototype = new prototype();
		$time = time();
		//$prototypeArray['createtime']=date('Y-m-d H:i:s', $time);
		$prototypeid = $_prototype->add($prototypeArray);		
		return $prototypeid;
	}

	//更新原型
	static function updatePrototype($prototypeid = 0,$prototypeArray=''){
		$id = (int)$prototypeid;
		if($id < 1) return false;
		$_prototype = new prototype();
		$prototypeid = $_prototype->editBy($prototypeArray, "prototypeid=".$id);
		return $prototypeid;
	}

	//获取原型列表
	static function getPrototypeList($sql = '', $page = 1, $pagesize = PAGE_SIZE){
		$offset = ($page - 1) * $pagesize;
		$_prototype = new prototype();
		$return = $_prototype->getList($sql, '', $offset, $pagesize);
		return $return;
	}

	//获取工作流详情
	static function getWorkflowInfo($workflowid = 0){
		$id = (int)$workflowid;
		if($id < 1) return false;
		$_workflow = new workflow();
		$info = $_workflow->getOne($id);
		$return = $info;
		return $return;	
	}

	//根据条件获取工作流详情
	static function getWorkflowInfoBy($sql = ''){
		$id = (int)$workflowid;
		if($id < 1) return false;
		$_workflow = new workflow();
		$info = $_workflow->getOne($id);
		$return = $info;
		return $return;	
	}

	//添加工作流
	static function addWorkflow($workflowArray=''){
		$_workflow = new workflow();
		self::getInstance();
		$time = time();
		//$workflowArray['createtime']=date('Y-m-d H:i:s', $time);
		$workflowid = $_workflow->add($workflowArray);
		return $workflowid;	
	}

	//更新工作流
	static function updateWorkflow($workflowid = 0,$workflowArray=''){
		$id = (int)$workflowid;
		if($id < 1) return false;
		$_workflow = new workflow();
		$workflowid = $_workflow->editBy($workflowArray, "workflowid=".$id);
		return $workflowid;	
	}

	//获取工作流列表
	static function getWorkflowList($sql = '', $page = 1, $pagesize = PAGE_SIZE){
		$offset = ($page - 1) * $pagesize;
		$_workflow = new workflow();
		$return = $_workflow->getList($sql, '', $offset, $pagesize);
		return $return;	
	}

	//根据工作流获取原型列表
	static function getPrototypeListByWorkflow($uid = 0){
		$uid = (int)$uid;
		$_user = Base::Create('user');
		$_workflow = new workflow();
		$_prototype = new prototype();
		$_former = new former();
		$prototypeArray = array();
		$formerArray = array();
		$userInfo = array();
		if($uid > 0){
			$userInfo = $_user->getInfoById($uid);
			$sql = " uid = ".$uid." ";
			//或者属于某个组织
			if($userInfo['organizations']) $sql .= "or organizationid in ( ".$userInfo['organizations']." ) ";
			//或者属于某个角色
			if($userInfo['roleids']) $sql .= "or roleid in ( ".$userInfo['roleids']." ) ";
			$workflowArray = $_workflow->getList($sql);
			foreach($workflowArray as $workflow){
				$prototypeInfo = $_prototype->getOne($workflow['prototypeid']);
				$prototypeArray[$prototypeInfo['prototypeid']] = $prototypeInfo;
			}
			foreach($prototypeArray as $prototype){
				$formerInfo = $_former->getOne($prototype['formerid']);
				$formerArray[$formerInfo['formerid']] = $formerInfo;
			}
			foreach($prototypeArray as $prototype){
				$formerArray[$prototype['formerid']]['prototypes'][$prototype["prototypeid"]] = $prototype;
			}
		}
		$return = $formerArray;
		return $return;
	}

	//获取字段详情
	static function getFieldInfo($fieldid = 0){
		$id = (int)$fieldid;
		if($id < 1) return false;
		$_field = new field();
		$info = $_field->getOne($id);
		$return = $info;
		return $return;
	}

	//添加字段
	static function addField($fieldArray=''){
		$_field = new field();
		self::getInstance();
		$time = time();
		//$fieldArray['createtime']=date('Y-m-d H:i:s', $time);
		$fieldid = $_field->add($fieldArray);
		self::getInstance();
		self::$_instance->alterCacheTable($fieldArray['prototypeid']);
		return $fieldid;
	}

	//更新字段
	static function updateField($fieldid = 0,$fieldArray=''){
		$id = (int)$fieldid;
		if($id < 1) return false;
		$_field = new field();
		$fieldid = $_field->editBy($fieldArray, "fieldid=".$id);
		$fieldInfo = $_field->getOne($id);
		self::getInstance();
		self::$_instance->alterCacheTable($fieldInfo['prototypeid']);
		return $fieldid;
	}

	//获取字段列表
	static function getFieldList($sql = '', $page = 1, $pagesize = PAGE_SIZE){
		$offset = ($page - 1) * $pagesize;
		$_field = new field();
		$return = $_field->getList($sql, ' listorder DESC ', $offset, $pagesize);
		return $return;
	}

	//更改数据缓存表
	static function alterCacheTable($prototypeid = 0){
		$id = (int)$prototypeid;
		if($id < 1) return false;
		$_prototype = new prototype();
		$_field = new field();
		$prototypeInfo = $_prototype->getOne($prototypeid);
		$tableName = TABLEPRE.DATA_TABLE_PRE.$prototypeInfo['name'];
		$isExistTable = $_field->doSQL("SHOW TABLES LIKE '".$tableName."' ");
		if(empty($isExistTable)){
			$sql = str_replace("data_table_init_name", $tableName, DATA_TABLE_INIT_SQL);
			$sql = str_replace("data_table_id", "id", $sql);
			$_field->doSQL($sql);
		}
		$fields = $_field->getList('prototypeid='.$prototypeid, '', 0, 100);
		foreach($fields as $field){
			$existField = $_field->doSQL("Describe ".$tableName." ".$field['name']." ;");
			$fieldSort = $field['sort'];
			if($field['size']) $fieldSort .= "(".$field['size'].")";
			if(empty($existField)){
				$_field->doSQL("ALTER TABLE ".$tableName." ADD ".$field['name']." ".$fieldSort." NOT NULL");
			}elseif($existField[0]['Field'] == $field['name']){
				if($existField[0]['Type'] != $fieldSort) $_field->doSQL("ALTER TABLE ".$tableName." modify ".$field['name']." ".$fieldSort." NOT NULL");			
			}
		}
		return true;
	}

	//添加缓存表数据
	static function addCacheTable($prototypeid = 0, $cacheTableArray = array()){
		$id = (int)$prototypeid;
		if($prototypeid < 1) return false;
		if(empty($cacheTableArray)) return false;
		$_prototype = new prototype();
		$prototypeInfo = $_prototype->getOne($prototypeid);
		$tableName = TABLEPRE.DATA_TABLE_PRE.$prototypeInfo['name'];
		$csql1 = $csql2 = $cs = "";
		$fsql1 = $fsql2 = $fs = "";
		$data = $cacheTableArray;
		foreach($data as $key=>$value){
			$csql1 .= $cs."`".$key."`";
			$csql2 .= $cs."'".$value."'";
			$cs = ",";
		}
		$isdone = $_prototype->db->query("INSERT INTO ".$tableName." ($csql1) VALUES($csql2) ;");
		if($isdone){
			$returnid = $_prototype->db->insert_id();
			$_log = Base::Create('log');
			$_log->logThis(" add ".$csql2." to ".$prototypeInfo['title']." ");
		}
		return $returnid;
	}

	//更新缓存表数据
	static function updateCacheTable($cachetableid = 0, $prototypeid = 0, $cacheTableArray = array()){
		$id = (int)$prototypeid;
		$cacheid = (int)$cachetableid;
		if($id < 1) return false;
		if($cacheid < 1) return false;
		if(empty($cacheTableArray)) return false;
		$_prototype = new prototype();
		$prototypeInfo = $_prototype->getOne($id);
		$tableName = TABLEPRE.DATA_TABLE_PRE.$prototypeInfo['name'];
		$sql = $s = "";
		$fsql = $fs = "";
		$data = $cacheTableArray;
		foreach($data as $key=>$value){
			$sql .= $s."`".$key."`"."='".$value."'";
			$s = ",";
		}
		$isdone = $_prototype->db->query("UPDATE ".$tableName." SET ".$sql." WHERE id = '".$cacheid."' ;");
		$_log = Base::Create('log');
		$_log->logThis(" update ".$sql." to ".$prototypeInfo['title']." ");
		return $isdone;
	}

	//获取缓存数据列表
	static function getListFromCacheTable($prototypeid = 0, $sql = '', $page = 1, $pagesize = PAGE_SIZE){
		$id = (int)$prototypeid;
		if($prototypeid < 1) return false;
		$_prototype = new prototype();
		$prototypeInfo = $_prototype->getOne($prototypeid);
		$tableName = TABLEPRE.DATA_TABLE_PRE.$prototypeInfo['name'];
		$sql = $sql ? " where ".$sql : "";
		$dosql = "select * from ".$tableName." ".$sql." order by id desc limit ".($page-1)*$pagesize.", ".$pagesize.";";
		$return = $_prototype->doSQL($dosql);
		return $return;
	}

	//获取缓存数据详情
	static function getInfoFromCacheTable($prototypeid = 0, $sql = ''){
		$id = (int)$prototypeid;
		if($prototypeid < 1) return false;
		$_prototype = new prototype();
		$prototypeInfo = $_prototype->getOne($prototypeid);
		$tableName = TABLEPRE.DATA_TABLE_PRE.$prototypeInfo['name'];
		$sql = $sql ? " where ".$sql : "";
		$dosql = "select * from ".$tableName." ".$sql." limit 0,1;";
		$return = $_prototype->doSQL($dosql);
		return $return[0];
	
	}

	//获取缓存数据总数
	static function getCacheTableCount($prototypeid = 0, $sql = ''){
		$id = (int)$prototypeid;
		if($prototypeid < 1) return false;
		$_prototype = new prototype();
		$prototypeInfo = $_prototype->getOne($prototypeid);
		$tableName = TABLEPRE.DATA_TABLE_PRE.$prototypeInfo['name'];
		$sql = $sql ? " where ".$sql : "";
		$dosql = "select count(*) as total from ".$tableName." ".$sql." ;";
		$return = $_prototype->doSQL($dosql);
		return $return[0]['total'];
	}

}

?>