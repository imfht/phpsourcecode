<?php
/*
 * 销售机会类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class CstChance extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function cst_chance(){
	
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		
		$where_str = " s.cusID=c.id and s.create_userID in (".SYS_USER_VIEW.")";
		
		$cusID 		= $this->_REQUEST("cusID");
		$cus_name   = $this->_REQUEST("cus_name");
		if(!empty($cusID) ){
			$where_str .=" and s.cusID='$cusID'";
		}
		if( !empty($searchValue) ){
			$where_str .=" and s.$searchKeyword like '%$searchValue%'";
		}
		if( !empty($cus_name) ){
			$where_str .=" and c.name like '%$cus_name%'";
		}		
		if( !empty($bdt) ){
			$where_str .=" and adt >= '$bdt'";
		}			
		if( !empty($edt) ){
			$where_str .=" and adt < '$edt'";
		}	
		//**************************************************************************
		$countSql    = "select c.name as cst_name ,s.* from cst_chance as s,cst_customer as c where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select c.name as cst_name ,s.* from cst_chance as s,cst_customer as c
						where $where_str 
						order by s.id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$status		 = $this->cst_chance_status();
		foreach($list as $key=>$row){
			$list[$key]['salestage_name']	=$this->L("CstDict")->cst_dict_get_name($row['salestage']);
			$list[$key]['linkman_name']	 	=$this->L("CstLinkman")->cst_linkman_get_name($row['linkmanID']);
			$list[$key]['status_name']	 	=$status[$row['status']];
			$list[$key]['create_user_name']	=$this->L("User")->user_get_name($row['create_userID']);
		}
		$assignArray = array('list'=>$list,'cusID'=>$cusID,'cus_name'=>$cus_name,
						"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
		
	}
	
	public function cst_chance_show(){
			$assArr = $this->cst_chance();
			$smarty = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('cst_chance/cst_chance_show.html');	
	}	
	
	public function cst_chance_show_box(){
			$assArr  			= $this->cst_chance();
			$assArr["dict"] 	= $this->L("CstDict")->cst_dict_arr();
			$assArr["linkman"] 	= $this->L("CstLinkman")->cst_linkman_arr();
			$assArr["status"] 	= $this->cst_chance_status();
			$assArr["users"] 	= $this->L("User")->user_arr();
			$smarty  			= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('cst_chance/cst_chance_show_box.html');	
	}	
	
	public function cst_chance_add(){
		$cusID 		= $this->_REQUEST("cusID");
		$cus_name 	= $this->_REQUEST("cus_name");		
		if(empty($_POST)){
			$status = $this->cst_chance_status_select("status");
			if($cusID>0){ 
				$cus_name=$this->L("Customer")->customer_get_name($cusID);
			}
			$smarty = $this->setSmarty();
			$smarty->assign(array("status"=>$status,"cusID"=>$cusID,"cus_name"=>$cus_name));
			$smarty->display('cst_chance/cst_chance_add.html');	
		
		}else{
			$rtn=$this->cst_chance_add_save();
			if($rtn>0){
				$this->L("Common")->ajax_json_success("操作成功",'2','/CstChance/cst_chance_show/');	
			}	
		}
	}		

	public function cst_chance_add_save(){
		$cusID 		= $this->_REQUEST("cusID");
		$cus_name	= $this->_REQUEST("cus_name");		
		$dt			= date("Y-m-d H:i:s",time());
		$cusID    = $this->_REQUEST("org_id");
		$linkmanID	= $this->_REQUEST("linkman_id");
		$salestage 	= $this->_REQUEST("salestage_id");
		$salemode  = $this->_REQUEST("salemode_id");
		$sql 		= "insert into cst_chance(cusID,salestage,linkmanID,finddt,billdt,
											money,chance,status,title,intro,adt,
											create_userID) 
							values('$cusID','$salestage','$linkmanID','$_POST[finddt]','$_POST[billdt]',
								'$_POST[money]','$_POST[chance]','$_POST[status]','$_POST[title]','$_POST[intro]','$dt',
								'".SYS_USER_ID."');";
			$rtn=$this->C($this->cacheDir)->update($sql);	
			return $rtn;
	}	

	public function cst_chance_modify(){
		$id	  	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from cst_chance where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$customer   = $this->L("Customer")->customer_arr();
			$linkman    = $this->L("CstLinkman")->cst_linkman_arr();
			$dict	    = $this->L("CstDict")->cst_dict_arr();
			$status 	= $this->cst_chance_status_select("status",$one["status"]);
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"customer"=>$customer,"linkman"=>$linkman,"dict"=>$dict,"status"=>$status));
			$smarty->display('cst_chance/cst_chance_modify.html');	
		}else{//更新保存数据
			$cusID   = $this->_REQUEST("org_id");
			$linkmanID   = $this->_REQUEST("linkman_id");
			$salestage   = $this->_REQUEST("salestage_id");
			$sql= "update cst_chance set 
							cusID='$cusID',linkmanID='$linkmanID',salestage='$salestage',
							finddt='$_POST[finddt]',billdt='$_POST[billdt]',money='$_POST[money]',chance='$_POST[chance]',
							status='$_POST[status]',title='$_POST[title]',intro='$_POST[intro]'
			 		where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功",'2','/CstChance/cst_chance_show/');				
		}
	}
	
	public function cst_chance_del(){
		$id	  = $this->_REQUEST("ids");
		$sql  = "delete from cst_chance where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/CstChance/cst_chance_show/");	
	}	
	
	public function cst_chance_select(){
		$cusID   = $this->_REQUEST("cusID");
		$sql	="select id,title from cst_chance where cusID='$cusID' order by id asc;";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		echo json_encode($list);
	}	
	public function cst_chance_opt($cusID,$inputdiv,$optvalue=null){
		$where 	=empty($cusID)?"":"where cusID='$cusID'";
		$sql	="select id,name from cst_chance $where order by sort asc;";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		$opthtml="<select name='$inputdiv'>";
		foreach($list as $row){
        $opthtml .="<option value='".$row['id']."'>".$row['name']."</option>";
		}
		$opthtml .='</select>';
		return $opthtml;
	}
	public function cst_chance_arr($cusID=""){
		$rtArr  =array();
		$where  =empty($cusID)?"":" where cusID='$cusID'";
		$sql	="select id,title from cst_chance $where ";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$rtArr[$row["id"]]=$row["title"];
			}
		}
		return $rtArr;
	}
	
	//返回字典名称
	public function cst_chance_get_name($id){
		if(empty($id)) $id=0;
		$sql  ="select id,title from cst_chance where id in ($id)";	
		$list =$this->C($this->cacheDir)->findAll($sql);
		$str  ="";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "|-".$row["title"]."&nbsp;";
			}
		}
		return $str;
	}
	
	public function cst_chance_status(){
		return array("1"=>"跟踪","2"=>"成功","3"=>"失败","4"=>"搁置","5"=>"失效");
		    
	}
	public function cst_chance_status_select($inputname,$value=""){
		$data=$this->cst_chance_status();
		$string ="<select name='$inputname'>";
		foreach($data as $key=>$va){
			$string.="<option value='$key'";
			if($key==$value) $string.=" selected";
			$string.=">".$va."</option>";
		}
		$string.="</select>";
		return $string;
	}		
			
}//end class
?>