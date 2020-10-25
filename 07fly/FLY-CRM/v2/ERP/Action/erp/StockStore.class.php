<?php
/*
 * erp.StockStore 仓库管理
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class StockStore extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/sysmanage/Auth');
	}	
	
	public function stock_store(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		
		$countSql    = "select store_id from stock_store";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($pageNum-1)*$pageSize;
		$sql		 = "select * from stock_store order by sort asc, store_id desc limit $beginRecord,$pageSize";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);	
		return $assignArray;
	}
	public function stock_store_json(){
		$assArr  = $this->stock_store();
		echo json_encode($assArr);
	}	
	public function stock_store_show(){
		$assArr  = $this->stock_store();
		$smarty  = $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('erp/stock_store_show.html');	
	}
	public function stock_store_add(){
		if(empty($_POST)){
			$smarty     = $this->setSmarty();
			$smarty->display('erp/stock_store_add.html');	
		}else{
			$into_data=array(
						'name'=>$this->_REQUEST("name"),
						'sort'=>$this->_REQUEST("sort"),
						'visible'=>$this->_REQUEST("visible"),
						'create_user_id'=>SYS_USER_ID,
						'create_time'=>NOWTIME,
					);
			$this->C($this->cacheDir)->insert('stock_store',$into_data);
			$this->L("Common")->ajax_json_success("操作成功");		
		}
	}		
	public function stock_store_modify(){
		$store_id	 = $this->_REQUEST("store_id");
		if(empty($_POST)){
			$sql 		= "select * from stock_store where store_id='$store_id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('erp/stock_store_modify.html');	
		}else{
			$into_data=array(
						'name'=>$this->_REQUEST("name"),
						'sort'=>$this->_REQUEST("sort"),
						'visible'=>$this->_REQUEST("visible"),
					);
			$this->C($this->cacheDir)->modify('stock_store',$into_data,"store_id='$store_id'");
			$this->L("Common")->ajax_json_success("操作成功");			
		}
	}	
	public function stock_store_del(){
		$store_id = $this->_REQUEST("store_id");
		$sql  = "delete from stock_store where store_id in($store_id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");	
	}	

	//下拉选择回放数据
	public function stock_store_select(){
		$sql	="select * from stock_store where visible='1' order by sort asc;";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		return $list;
	}
	
	//传入ID卡号
	public function stock_store_get_one($store_id){
		if(empty($store_id)) $store_id=0;
		$sql	="select * from stock_store where store_id='$store_id'";
		$one	=$this->C($this->cacheDir)->findOne($sql);
		return $one;
	}
	
	//排序
	public function stock_store_modify_sort(){
		$store_id = $this->_REQUEST("store_id");
		$sort = $this->_REQUEST("sort");
		$upt_data=array(
			'sort'=>$sort,
		);
		$this->C($this->cacheDir)->modify('stock_store',$upt_data,"store_id='$store_id'");
		$this->L("Common")->ajax_json_success("操作成功");	
	}
	//开关
	public function stock_store_modify_visible(){
		$store_id = $this->_REQUEST("store_id");
		$visible = $this->_REQUEST("visible");
		$upt_data=array(
			'visible'=>$visible,
		);
		$this->C($this->cacheDir)->modify('stock_store',$upt_data,"store_id='$store_id'");
		$this->L("Common")->ajax_json_success("操作成功");	
	}	
}//end class
?>