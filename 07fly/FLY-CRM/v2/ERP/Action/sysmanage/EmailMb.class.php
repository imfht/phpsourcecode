<?php
/*
 *
 * sysmanage.EmailMB  邮箱模板管理   
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

class EmailMb extends Action {
	private $cacheDir = ''; //缓存目录
	public
	function __construct() {
		_instance( 'Action/sysmanage/Auth' );
	}
/*邮件模板*/
	//显示所有
	public function email_mb(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		//**************************************************************************
		$where_str = "0=0 ";
		if( !empty($searchValue) ){
			$where_str .=" and $searchKeyword like '%$searchValue%'";
		}
		
		$countSql	= 'select * from fly_config_email_mb';
		$totalCount	= $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数	
		$beginRecord= ($pageNum-1)*$pageSize;
		$sql   		= "select * from fly_config_email_mb where $where_str order by id desc limit $beginRecord,$pageSize";		
		$list 		= $this->C($this->cacheDir)->findAll($sql);//查询结果为二维数组，需foreach循
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);
		return $assignArray;
	}

	public function email_mb_json(){
		$list	=$this->email_mb();
		echo json_encode($list);
	}	
	public function email_mb_show(){
		$smarty =$this->setSmarty();
		$smarty->display('sysmanage/email_mb_show.html'); 
	}	
	//添加记录		
	public function email_mb_add(){	
		if(empty($_POST)){	
			$smarty =$this->setSmarty();
			$smarty->display('sysmanage/email_mb_add.html');     		      
		}else{
			$content= ($this->_REQUEST("content"));
			$sql	= "select name from fly_config_email_mb where name='$_POST[name]'";
			$one 	= $this->C($this->cacheDir)->findOne($sql);
			if(empty($one)){
				$now	=date("Y-m-d H:i:s",time());
				$sql	="insert into fly_config_email_mb (name,content,adddatetime) 
								  values ('$_POST[name]','$content','$now')";	
				if($this->C($this->cacheDir)->update($sql)){
					$this->location("操作成功",'/sysmanage/EmailMb/email_mb_show/');
				}			
			}else{
				$this->location("输入名称已经存在",'/sysmanage/EmailMb/email_mb_show/','1');
				exit;		
			}
		}		
	}	
	//修改记录	
	public function email_mb_modify (){
		if(empty($_POST)){
			$sql	= "select * from fly_config_email_mb where id=".$_GET['id'];				 
			$one 	= $this->C($this->cacheDir)->findOne($sql);
			$one["content"]	= ($one["content"]);
			$assArr	=array('one'=>$one);
			$smarty =$this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('sysmanage/email_mb_modify.html');
		}else{		
			$content= ($this->_REQUEST("content"));
			$sql="update fly_config_email_mb set
						name = '$_POST[name]',
						content = '$content'
				  where id=".$_GET['id'];	
			if($this->C($this->cacheDir)->update($sql)>=0){
				$this->location("操作成功",'/sysmanage/EmailMb/email_mb_show/');
			}			 
		}			
	}
	//删除记录
	public function email_mb_del (){
		$id	= $this->_REQUEST("ids");
		$sql="delete from fly_config_email_mb where id in (".$id.")";											 
		if($this->C($this->cacheDir)->update($sql)>0){
			$this->L("Common")->ajax_json_success("操作成功",'1','/EmailMb/email_mb_show/');
		}
		
	}
	/*********************************************************************
	 * 根据传入的ID值查询出相对的下拉选择框
	 * 
	*/
	public function email_mb_option($inputname,$value=null){
		$sql  	="select * from fly_config_email_mb";	
		$list 	=$this->C($this->cacheDir)->findAll($sql);
		$string	="<select name='$inputname'  class='combox'>";
		$string.="<option value='0'>选择邮件模板</option>";
		foreach($list as $key=>$v){
			$string .="<option value='".$v['id']."'";
			if($v['id']==$value) $string.=" selected";
			$string .=">".$v['name']."</option>";
		}
		$string .="</select>";
		return $string;
	}
	public function email_mb_checkbox($inputname,$value=null){
		$sql  	="select * from fly_config_email_mb";
		$value  =explode(",",$value);
		$list 	=$this->C($this->cacheDir)->findAll($sql);
		$string ="";		
		foreach($list as $key=>$v){
			$string	.="<p><input type='checkbox' name='".$inputname."[]' value='".$v["id"]."' " ;
			if(in_array($v["id"],$value)) $string .=" checked";
			$string .="> ".$v["name"]."&nbsp;</p>";
		}
		return $string;
	}
	/*********************************************************************
	 * 根据传入的ID值查询出相对的名称
	 * ex:$id = 1,3,5, 
	*/
	
	public function email_mb_get_name($id){
		if(!$id) $id=0;
		$sql  =	"select * from fly_config_email_mb where id in ($id)";	
		$list =	$this->C($this->cacheDir)->findAll($sql);
		$str  =	"";
		if(is_array($list)){
			foreach($list as $key=>$row){
				$str .= "$key:".$row["name"]."&nbsp;";
			}
		}
		return $str;
	}

	public function email_mb_get_one($id){
		$sql  =	"select * from fly_config_email_mb where id='$id'";	
		$one  =	$this->C($this->cacheDir)->findOne($sql);
		$one["content"]	= ($one["content"]);
		return $one;
	}		

	//增加短信发送日志
	public function email_log_add($info,$email,$ipaddr=null){
		$nowtime = date("Y-m-d H:i:s",time());
		$sql 	 = "insert into fly_config_email_log(email,content,ipaddr,adddatetime) values('$email','$info','$ipaddr','$nowtime')";
		if($this->C($this->cacheDir)->update($sql)<=0){
			return false;
		}else{
			return true;
		}
	}	
	
	public function email_log_get_last($email,$ipaddr){
		$sql	= "select * from fly_config_email_log where email='$email' or ipaddr='$ipaddr' order by id desc limit 0,1";	
		$one	= $this->C($this->cacheDir)->findOne($sql);
		return $one;
			
	}
	
	//获取操作日志
	public function email_log(){
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;		

		//用户查询参数
		$searchKeyword= $this->_REQUEST("searchKeyword");
		$searchValue  = $this->_REQUEST("searchValue");
		$startdate    = $this->_REQUEST("startdate");
		$enddate	  = $this->_REQUEST("enddate");	
		$editor	  	  = $this->_REQUEST("org_account");	
		
		$where 		  = "0=0 ";
		if(!empty($searchValue)){
			$where .= " and $searchKeyword like '%$searchValue%'";
		}	
		if($startdate){
			$where .=" and adddatetime>'$startdate' ";
		}
		if($enddate){
			$where .=" and adddatetime<='$enddate' ";
		}	
		if($editor){
			$where .=" and editor='$editor' ";
		}				
		$countSql	= "select * from fly_config_email_log where $where";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;	
		$sql		= "select * from fly_config_email_log  where $where order by id desc limit $beginRecord,$numPerPage";	
		$list		= $this->C($this->cacheDir)->findAll($sql);//查询结果为二维数组，需foreach循环
		$assignArray=array('list'=>$list,'searchKeyword'=>$searchKeyword,'searchValue'=>$searchValue,
							'startdate'=>$startdate,'enddate'=>$enddate,'editor'=>$editor,
							"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage
							);				
					
		return $assignArray;
	}
	//调用显示
	public function email_log_show(){
		$list	= $this->email_log();
		$smarty = $this->setSmarty();
		$smarty->assign($list);//框架变量注入同样适用于smarty的assign方法
		$smarty->display('email/email_log.html');			
	}
	//删除选中记录
	public function email_log_del (){
		$id	=$this->_REQUEST("ids");	
		$sql="delete from fly_config_email_log where id in (".$id.");";											 
		if($this->C($this->cacheDir)->update($sql)>=0){
			$this->L("Common")->ajax_json_success("操作成功",'1','/Email/email_log_show/');	
		}	
	}	
	
	//删除全部记录
	public function email_log_del_all (){
		$sql="delete from fly_config_email_log";							 
		if($this->C($this->cacheDir)->update($sql)>=0){
			$this->L("Common")->ajax_json_success("操作成功",'1','/Email/email_log_show/');	
		}	
	}	
	
	
} //end class
?>