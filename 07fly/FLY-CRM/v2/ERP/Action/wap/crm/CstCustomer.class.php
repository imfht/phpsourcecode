<?php
/*
 *
 * wap.crm.CstCustomer  客户管理   
 *
 * =========================================================
 * 零起飞网络 - 专注于网站建设服务和行业系统开发
 * 以质量求生存，以服务谋发展，以信誉创品牌 !
 * ----------------------------------------------
 * @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license    For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author ：kfrs <goodkfrs@QQ.com> 574249366
 * @version ：1.0
 * @link ：http://www.07fly.top 
 */	
class CstCustomer extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/wap/Auth');
		$this->dict=_instance('Action/wap/crm/CstDict');
	}	
	
	public function cst_customer(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$name	   = $this->_REQUEST("name");
		$conn_time	= $this->_REQUEST("conn_time");
		$next_time	= $this->_REQUEST("next_time");
		$edt   	  = $this->_REQUEST("edt");
		$orderField = $this->_REQUEST("orderField");
		$orderDirection = $this->_REQUEST("orderDirection");
		
		$where_str = " c.customer_id > 0";
		
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		if( !empty($searchKeyword) ){
			$where_str .=" and c.name like '%$searchKeyword%'";
		}
		if( !empty($name) ){
			$where_str .=" and c.name like '%$name%'";
		}
		
		$order_by="order by";
		if( $orderField=='by_next_time' ){
			$order_by .=" c.next_time $orderDirection";
		}else if($orderField=='by_conn_time'){
			$order_by .=" c.conn_time $orderDirection";
		}else if($orderField=='by_customer'){
			$order_by .=" c.customer_id $orderDirection";
		}else{
			$order_by .=" c.customer_id desc";
		}
		
		//**************************************************************************
		$countSql   = "select c.* from cst_customer  as c where $where_str";
		$totalCount	 = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord= ($pageNum-1)*$pageSize;//计算开始行数
		$sql		 = "select c.* from cst_customer as c where $where_str $order_by limit $beginRecord,$pageSize";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$dict		 = $this->dict->cst_dict_arr();
		foreach($list as $key=>$row){
//			$list[$key]['source_name']=($row['source']>0)?$dict[$row['source']]:"";
//			$list[$key]['level_name']=($row['level']>0)?$dict[$row['level']]:"";
//			$list[$key]['trade_name']=($row['trade']>0)?$dict[$row['trade']]:"";
		}
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum,"statusCode"=>'200');		
		return $assignArray;
	}
	//返回客户id.名称主要用于下拉选择搜索
	public function cst_customer_list(){
		$sql ="select customer_id,name from cst_customer";
		$list=$this->C($this->cacheDir)->findAll($sql);
		return $list;
	}		
	public function cst_customer_json(){
		$assArr  = $this->cst_customer();
		echo json_encode($assArr);
	}	
	//客户列表显示
	public function cst_customer_show(){
			$smarty = $this->setSmarty();
			$smarty->display('crm/cst_customer_show.html');	
	}	

	//客户增加
	public function cst_customer_add(){
		if(empty($_POST)){
			$this->dict=_instance('Action/crm/CstDict');
			$trade=$this->dict->cst_dict_list('trade');//行业
			$ecotype =$this->dict->cst_dict_list('ecotype');//经济类型
			$source =$this->dict->cst_dict_list('source');//来源
			$level =$this->dict->cst_dict_list('level');//等级
			$smarty = $this->setSmarty();
			$smarty->assign(array("source"=>$source,"level"=>$level,"ecotype"=>$ecotype,"trade"=>$trade));
			$smarty->display('crm/cst_customer_add.html');	
		}
	}		
	//保存数据
	public function cst_customer_add_save(){
			$into_data=array(
				'conn_time'=>NOWTIME,
				'name'=>$this->_REQUEST("name"),
				'source'=>$this->_REQUEST("source"),
				'level'=>$this->_REQUEST("level"),
				'ecotype'=>$this->_REQUEST("ecotype"),
				'trade'=>$this->_REQUEST("trade"),
				'linkman'=>$this->_REQUEST("linkman"),
				'mobile'=>$this->_REQUEST("mobile"),
				'tel'=>$this->_REQUEST("tel"),
				'email'=>$this->_REQUEST("email"),
				'address'=>$this->_REQUEST("address"),
				'intro'=>$this->_REQUEST("intro"),
				'create_time'=>NOWTIME,
				'create_user_id'=>SYS_USER_ID,
			);
			$customer_id=$this->C($this->cacheDir)->insert('cst_customer',$into_data);
			if($customer_id>0){
				$upt_data=array(
					'name'=>$this->_REQUEST("linkman"),
					'mobile'=>$this->_REQUEST("mobile"),
					'tel'=>$this->_REQUEST("tel"),
					'email'=>$this->_REQUEST("email"),
					'customer_id'=>$customer_id,
					'create_time'=>NOWTIME,
					'create_user_id'=>SYS_USER_ID,
				);
				$this->C($this->cacheDir)->insert('cst_linkman',$upt_data);
				$this->L("Common")->ajax_json_success("操作成功");
			}else{
				$this->L("Common")->ajax_json_error("操作失败");
			}
	}	
	public function cst_customer_modify(){
		$customer_id	= $this->_REQUEST("customer_id");		
		if(empty($_POST)){
			$sql 	= "select * from cst_customer where customer_id='$customer_id'";
			$one 	= $this->C($this->cacheDir)->findOne($sql);	
			$trade	=$this->dict->cst_dict_list('trade');//行业
			$ecotype=$this->dict->cst_dict_list('ecotype');//经济类型
			$source =$this->dict->cst_dict_list('source');//来源
			$level	=$this->dict->cst_dict_list('level');//等级
			$smarty = $this->setSmarty();
			$smarty->assign(array("one"=>$one,"source"=>$source,"level"=>$level,"ecotype"=>$ecotype,"trade"=>$trade));
			$smarty->display('crm/cst_customer_modify.html');	
			
		}else{//更新保存数据
			$into_data=array(
				'conn_time'=>NOWTIME,
				'name'=>$this->_REQUEST("name"),
				'source'=>$this->_REQUEST("source"),
				'level'=>$this->_REQUEST("level"),
				'ecotype'=>$this->_REQUEST("ecotype"),
				'trade'=>$this->_REQUEST("trade"),
				'linkman'=>$this->_REQUEST("linkman"),
				'mobile'=>$this->_REQUEST("mobile"),
				'tel'=>$this->_REQUEST("tel"),
				'email'=>$this->_REQUEST("email"),
				'address'=>$this->_REQUEST("address"),
				'intro'=>$this->_REQUEST("intro")
			);
			$this->C($this->cacheDir)->modify('cst_customer',$into_data,"customer_id='$customer_id'");
			$this->L("Common")->ajax_json_success("操作成功");			
		}
	}
	
	public function cst_customer_detail(){
		$customer_id = $this->_REQUEST("customer_id");
		$one =$this->cst_customer_get_one($customer_id);
		$rtnArr =array('one'=>$one,'statusCode'=>'200');
		echo json_encode($rtnArr);		
	}
	
	public function cst_customer_del(){
		$customer_id = $this->_REQUEST("customer_id");
		$sql  = "delete from cst_customer where customer_id in ($customer_id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");	
	}	
			
	public function cst_customer_get_one($customer_id=""){
		if($customer_id){
			$sql = "select * from cst_customer where customer_id='$customer_id' ";
			$one = $this->C($this->cacheDir)->findOne($sql);	
			$dict= $this->dict->cst_dict_arr();
			$one['source_name']=($one['source']>0)?$dict[$one['source']]:"";
			$one['ecotype_name']=($one['ecotype']>0)?$dict[$one['ecotype']]:"";
			$one['level_name']=($one['level']>0)?$dict[$one['level']]:"";
			$one['trade_name']=($one['trade']>0)?$dict[$one['trade']]:"";
			return $one;
		}	
	}	

		
}//end class
?>