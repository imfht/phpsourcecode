<?php
/*
 * 邮件群发类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
 
class EmailSend extends Action{	
	private $cacheDir='';//缓存目录
	
	public function __construct() {
		 _instance('Action/Auth');
	}	
	
	
	//显示所有
	public function email_from(){
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		$where_str = "0=0 ";
		if( !empty($searchValue) ){
			$where_str .=" and $searchKeyword like '%$searchValue%'";
		}
		
		$countSql	= 'select * from email_from';
		$totalCount	= $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数	
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql   		= "select * from email_from where $where_str order by id desc limit $beginRecord,$numPerPage";		
		$list 		= $this->C($this->cacheDir)->findAll($sql);//查询结果为二维数组，需foreach循
		//查询地区
		$group = array();
		if(is_array($list)){
			foreach($list as $key=>$row){
				$group[$row['id']] = $this->email_from_group_get_name($row['groupID']);
				
			}
		}	
		$assignArray=array('list'=>$list,'searchKeyword'=>$searchKeyword,'searchValue'=>$searchValue,
							'group'=>$group,
							"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage
						);//适合多个变量注入
		return $assignArray;
	}

	
	public function email_from_show(){
		$list			=$this->email_from();
		$smarty =$this->setSmarty();
		$smarty->assign($list);
		$smarty->display('email/email_from.html'); 
	}	
	//添加记录		
	public function email_from_add(){	
		if(empty($_POST)){	
			$groupoption=$this->email_from_group_option("groupID",$value=null);
			$smarty =$this->setSmarty();
			$smarty->assign(array("groupoption"=>$groupoption));
			$smarty->display('email/email_from_add.html');     		      
		}else{
			$sql="insert into email_from (account,password,server,port,intro,groupID) 
							  values ('$_POST[account]','$_POST[password]','$_POST[server]',
									 '$_POST[port]','$_POST[intro]','$_POST[groupID]')";	 					
			if($this->C($this->cacheDir)->update($sql)){
				$this->L("Common")->ajax_json_success("操作成功",'1','/EmailSend/email_from_show/');
			}	
		}		
	}	
	//添加记录		
	public function email_from_add_more(){	
		if(empty($_POST)){	
			$groupoption=$this->email_from_group_option("groupID",$value=null);
			$smarty =$this->setSmarty();
			$smarty->assign(array("groupoption"=>$groupoption));
			$smarty->display('email/email_from_add_more.html');     		      
		}else{
			$content	=$this->_REQUEST("content");
			$contentArr	=explode("\n",$content);	
			if(!empty($contentArr) && is_array($contentArr)){
				foreach($contentArr as $key=>$row){
					$rowArr	=explode(",",$row);
					if($rowArr[0] && $rowArr[1] && $rowArr[2] && $rowArr[3] && $rowArr[4]){
						$sql="insert into email_from (name,account,password,server,port,groupID) 
							  values ('".$rowArr[0]."','".$rowArr[1]."','".$rowArr[2]."','".$rowArr[3]."','".$rowArr[4]."','$_POST[groupID]')";
						$this->C($this->cacheDir)->update($sql);
					}
				}
				
			} 	
			$this->L("Common")->ajax_json_success("操作成功",'1','/EmailSend/email_from_show/');
		}		
	}	
	
	//修改记录	
	public function email_from_modify (){
		if(empty($_POST)){
			$sql	= "select * from email_from where id=".$_GET['id'];				 
			$one 	= $this->C($this->cacheDir)->findOne($sql);
			$groupoption=$this->email_from_group_option("groupID",$one["groupID"]);
			$assArr	=array('one'=>$one,"groupoption"=>$groupoption);
			$smarty =$this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('email/email_from_modify.html');
		}else{		
			$sql="update email_from set
						account = '$_POST[account]',
						password = '$_POST[password]',
						server= '$_POST[server]',
						port= '$_POST[port]',
						groupID= '$_POST[groupID]',
						intro= '$_POST[intro]'
				  where id=".$_GET['id'];	
			if($this->C($this->cacheDir)->update($sql)>=0){
				$this->L("Common")->ajax_json_success("操作成功",'1','/EmailSend/email_from_show/');
			}			 
		}			
	}

	//删除记录
	public function email_from_del (){
		$id	= $this->_REQUEST("ids");
		$sql="delete from email_from where id in (".$id.")";											 
		if($this->C($this->cacheDir)->update($sql)>0){
			$this->L("Common")->ajax_json_success("操作成功",'1','/EmailSend/email_from_show/');
		}
		
	}	

/*发送地址分组*/
	//显示所有
	public function email_from_group(){
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		$where_str = "0=0 ";
		if( !empty($searchValue) ){
			$where_str .=" and $searchKeyword like '%$searchValue%'";
		}
		
		$countSql	= 'select * from email_from_group';
		$totalCount	= $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数	
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql   		= "select * from email_from_group where $where_str order by id desc limit $beginRecord,$numPerPage";		
		$list 		= $this->C($this->cacheDir)->findAll($sql);//查询结果为二维数组，需foreach循
		$assignArray=array('list'=>$list,'searchKeyword'=>$searchKeyword,'searchValue'=>$searchValue,
							"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage
						);//适合多个变量注入
		return $assignArray;
	}

	
	public function email_from_group_show(){
		$list			=$this->email_from_group();
		$smarty =$this->setSmarty();
		$smarty->assign($list);
		$smarty->display('email/email_from_group.html'); 
	}	
	//添加记录		
	public function email_from_group_add(){	
		if(empty($_POST)){	
			$smarty =$this->setSmarty();
			$smarty->display('email/email_from_group_add.html');     		      
		}else{
			$sql="insert into email_from_group (name) values('$_POST[name]')";	 					
			if($this->C($this->cacheDir)->update($sql)){
				$this->L("Common")->ajax_json_success("操作成功",'1','/EmailSend/email_from_group_show/');
			}	
		}		
	}	
	//修改记录	
	public function email_from_group_modify (){
		if(empty($_POST)){
			$sql	= "select * from email_from_group where id=".$_GET['id'];				 
			$one 	= $this->C($this->cacheDir)->findOne($sql);
			$assArr	=array('one'=>$one);
			$smarty =$this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('email/email_from_group_modify.html');
		}else{		
			$sql="update email_from_group set
						name = '$_POST[name]'
				  where id=".$_GET['id'];	
			if($this->C($this->cacheDir)->update($sql)>=0){
				$this->L("Common")->ajax_json_success("操作成功",'1','/EmailSend/email_from_group_show/');
			}			 
		}			
	}

	//删除记录
	public function email_from_group_del (){
		$id	= $this->_REQUEST("ids");
		$sql="delete from email_from_group where id in (".$id.")";											 
		if($this->C($this->cacheDir)->update($sql)>0){
			$this->L("Common")->ajax_json_success("操作成功",'1','/EmailSend/email_from_group_show/');
		}
		
	}
	/*********************************************************************
	 * 根据传入的ID值查询出相对的下拉选择框
	 * 
	*/
	public function email_from_group_option($inputname,$value=null){
		$sql  	="select * from email_from_group";	
		$list 	=$this->C($this->cacheDir)->findAll($sql);
		$string	="<select name='$inputname'  class='combox'>";
		foreach($list as $key=>$v){
			$string .="<option value='".$v['id']."'";
			if($v['id']==$value) $string.=" selected";
			$string .=">".$v['name']."</option>";
		}
		$string .="</select>";
		return $string;
	}
	/*********************************************************************
	 * 根据传入的ID值查询出相对的名称
	 * ex:$id = 1,3,5, 
	*/
	
	public function email_from_group_get_name($id){
		$sql  =	"select * from email_from_group where id in ($id)";	
		$list =	$this->C($this->cacheDir)->findAll($sql);
		$str  =	"";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "".$row["name"]."&nbsp;";
			}
		}
		return $str;
	}





/*邮件接收地址*/

	//显示所有
	public function email_receiver(){
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		$where_str = "0=0 ";
		if( !empty($searchValue) ){
			$where_str .=" and $searchKeyword like '%$searchValue%'";
		}
		
		$countSql	= 'select * from email_receiver';
		$totalCount	= $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数	
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql   		= "select * from email_receiver where $where_str order by id desc limit $beginRecord,$numPerPage";		
		$list 		= $this->C($this->cacheDir)->findAll($sql);//查询结果为二维数组，需foreach循
		$group = array();
		if(is_array($list)){
			foreach($list as $key=>$row){
				$group[$row['id']] = $this->email_receiver_group_get_name($row['groupID']);
				
			}
		}	
		$assignArray=array('list'=>$list,'searchKeyword'=>$searchKeyword,'searchValue'=>$searchValue,
							'group'=>$group,
							"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage
						);//适合多个变量注入
		return $assignArray;
	}

	
	public function email_receiver_show(){
		$list	=$this->email_receiver();
		$smarty =$this->setSmarty();
		$smarty->assign($list);
		$smarty->display('email/email_receiver.html'); 
	}	
	//添加记录		
	public function email_receiver_add(){	
		if(empty($_POST)){	
			$groupoption=$this->email_receiver_group_option("groupID",$value=null);
			$smarty =$this->setSmarty();
			$smarty->assign(array("groupoption"=>$groupoption));
			$smarty->display('email/email_receiver_add.html');     		      
		}else{

			$now	=date("Y-m-d H:i:s",time());
			$sql	="insert into email_receiver (name,account,groupID) 
							  values ('$_POST[name]','$_POST[account]','$_POST[groupID]')";		 					
			if($this->C($this->cacheDir)->update($sql)){//返回新增记录ID，用户可以返回值是否大于零作判断
				$this->L("Common")->ajax_json_success("操作成功",'1','/EmailSend/email_receiver_show/');
			}			

		}		
	}	
	
	//添加记录		
	public function email_receiver_add_more(){	
		if(empty($_POST)){	
			$groupoption=$this->email_receiver_group_option("groupID",$value=null);
			$smarty =$this->setSmarty();
			$smarty->assign(array("groupoption"=>$groupoption));
			$smarty->display('email/email_receiver_add_more.html');     		      
		}else{
			$content	=$this->_REQUEST("content");
			echo $content;
			$contentArr	=explode("\n",$content);	
			if(!empty($contentArr) && is_array($contentArr)){
				foreach($contentArr as $key=>$row){
					$rowArr	=explode(",",$row);
					if($rowArr[0] && $rowArr[1]){
						$sql="insert into email_receiver (name,account,groupID) 
							  values ('".$rowArr[0]."','".$rowArr[1]."','$_POST[groupID]')";
							  echo $sql;
						$this->C($this->cacheDir)->update($sql);
					}
				}
				
			} 
			$this->L("Common")->ajax_json_success("操作成功",'1','/EmailSend/email_receiver_show/');		

		}		
	}
	//修改记录	
	public function email_receiver_modify (){
		if(empty($_POST)){
			$sql	= "select * from email_receiver where id=".$_GET['id'];				 
			$one 	= $this->C($this->cacheDir)->findOne($sql);
			$groupoption=$this->email_receiver_group_option("groupID",$one["groupID"]);
			$assArr	=array('one'=>$one,"groupoption"=>$groupoption);
			$smarty =$this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('email/email_receiver_modify.html');
		}else{		
			$sql="update email_receiver set
						name = '$_POST[name]',
						account = '$_POST[account]',
						groupID = '$_POST[groupID]',
						intro = '$_POST[intro]'
				  where id=".$_GET['id'];	
			if($this->C($this->cacheDir)->update($sql)>=0){
				$this->L("Common")->ajax_json_success("操作成功",'1','/EmailSend/email_receiver_show/');
			}			 
		}			
	}

	//删除记录
	public function email_receiver_del (){
		$id	= $this->_REQUEST("ids");
		$sql="delete from email_receiver where id in (".$id.")";											 
		if($this->C($this->cacheDir)->update($sql)>0){
			$this->L("Common")->ajax_json_success("操作成功",'1','/EmailSend/email_receiver_show/');
		}
		
	}	
	/*********************************************************************
	 * 根据传入的ID值查询出相对的下拉选择框
	 * 
	*/
	public function email_receiver_option($inputname,$value=null){
		$sql  	="select * from email_receiver";	
		$list 	=$this->C($this->cacheDir)->findAll($sql);
		$string	="<select name='$inputname'  class='combox'>";
		$string.="<option value='0'>选择短信模板</option>";
		foreach($list as $key=>$v){
			$string .="<option value='".$v['id']."'";
			if($v['id']==$value) $string.=" selected";
			$string .=">".$v['name']."</option>";
		}
		$string .="</select>";
		return $string;
	}
	/*********************************************************************
	 * 根据传入的ID值查询出相对的名称
	 * ex:$id = 1,3,5, 
	*/
	
	public function email_receiver_get_name($id){
		$sql  =	"select * from email_receiver where id in ($id)";	
		$list =	$this->C($this->cacheDir)->findAll($sql);
		$str  =	"";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "".$row["name"]."&nbsp;";
			}
		}
		return $str;
	}

	public function email_receiver_get_one($id){
		$sql  =	"select * from email_receiver where id='$id'";	
		$one  =	$this->C($this->cacheDir)->findOne($sql);
		return $one;
	}	


/*发送地址分组*/
	//显示所有
	public function email_receiver_group(){
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		$where_str = "0=0 ";
		if( !empty($searchValue) ){
			$where_str .=" and $searchKeyword like '%$searchValue%'";
		}
		
		$countSql	= 'select * from email_receiver_group';
		$totalCount	= $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数	
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql   		= "select * from email_receiver_group where $where_str order by id desc limit $beginRecord,$numPerPage";		
		$list 		= $this->C($this->cacheDir)->findAll($sql);//查询结果为二维数组，需foreach循
		$assignArray=array('list'=>$list,'searchKeyword'=>$searchKeyword,'searchValue'=>$searchValue,
							"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage
						);//适合多个变量注入
		return $assignArray;
	}

	
	public function email_receiver_group_show(){
		$list			=$this->email_receiver_group();
		$smarty =$this->setSmarty();
		$smarty->assign($list);
		$smarty->display('email/email_receiver_group.html'); 
	}	
	//添加记录		
	public function email_receiver_group_add(){	
		if(empty($_POST)){	
			$smarty =$this->setSmarty();
			$smarty->display('email/email_receiver_group_add.html');     		      
		}else{
			$sql="insert into email_receiver_group (name) values('$_POST[name]')";	 					
			if($this->C($this->cacheDir)->update($sql)){
				$this->L("Common")->ajax_json_success("操作成功",'1','/EmailSend/email_receiver_group_show/');
			}	
		}		
	}	
	//修改记录	
	public function email_receiver_group_modify (){
		if(empty($_POST)){
			$sql	= "select * from email_receiver_group where id=".$_GET['id'];				 
			$one 	= $this->C($this->cacheDir)->findOne($sql);
			$assArr	=array('one'=>$one);
			$smarty =$this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('email/email_receiver_group_modify.html');
		}else{		
			$sql="update email_receiver_group set
						name = '$_POST[name]'
				  where id=".$_GET['id'];	
			if($this->C($this->cacheDir)->update($sql)>=0){
				$this->L("Common")->ajax_json_success("操作成功",'1','/EmailSend/email_receiver_group_show/');
			}			 
		}			
	}

	//删除记录
	public function email_receiver_group_del (){
		$id	= $this->_REQUEST("ids");
		$sql="delete from email_receiver_group where id in (".$id.")";											 
		if($this->C($this->cacheDir)->update($sql)>0){
			$this->L("Common")->ajax_json_success("操作成功",'1','/EmailSend/email_receiver_group_show/');
		}
		
	}
	/*********************************************************************
	 * 根据传入的ID值查询出相对的下拉选择框
	 * 
	*/
	public function email_receiver_group_option($inputname,$value=null){
		$sql  	="select * from email_receiver_group";	
		$list 	=$this->C($this->cacheDir)->findAll($sql);
		$string	="<select name='$inputname'  class='combox'>";
		foreach($list as $key=>$v){
			$string .="<option value='".$v['id']."'";
			if($v['id']==$value) $string.=" selected";
			$string .=">".$v['name']."</option>";
		}
		$string .="</select>";
		return $string;
	}
	/*********************************************************************
	 * 根据传入的ID值查询出相对的名称
	 * ex:$id = 1,3,5, 
	*/
	
	public function email_receiver_group_get_name($id){
		$sql  =	"select * from email_receiver_group where id in ($id)";	
		$list =	$this->C($this->cacheDir)->findAll($sql);
		$str  =	"";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "".$row["name"]."&nbsp;";
			}
		}
		return $str;
	}
	

/*邮件模板*/
	//显示所有
	public function email_mb(){
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		$where_str = "0=0 ";
		if( !empty($searchValue) ){
			$where_str .=" and $searchKeyword like '%$searchValue%'";
		}
		
		$countSql	= 'select * from email_mb';
		$totalCount	= $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数	
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql   		= "select * from email_mb where $where_str order by id desc limit $beginRecord,$numPerPage";		
		$list 		= $this->C($this->cacheDir)->findAll($sql);//查询结果为二维数组，需foreach循
		$assignArray=array('list'=>$list,'searchKeyword'=>$searchKeyword,'searchValue'=>$searchValue,
							"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage
						);//适合多个变量注入
		return $assignArray;
	}

	
	public function email_mb_show(){
		$list	=$this->email_mb();
		$smarty =$this->setSmarty();
		$smarty->assign($list);
		$smarty->display('email/email_mb.html'); 
	}	
	//添加记录		
	public function email_mb_add(){	
		if(empty($_POST)){	
			$smarty =$this->setSmarty();
			$smarty->display('email/email_mb_add.html');     		      
		}else{
			$content= ($this->_REQUEST("content"));
			$sql	= "select name from email_mb where name='$_POST[name]'";
			$one 	= $this->C($this->cacheDir)->findOne($sql);
			if(empty($one)){
				$now	=date("Y-m-d H:i:s",time());
				$sql	="insert into email_mb (name,content,adddatetime) 
								  values ('$_POST[name]','$content','$now')";		 					
				if($this->C($this->cacheDir)->update($sql)){
					$this->L("Common")->ajax_json_success("操作成功",'1','/EmailSend/email_mb_show/');
				}			
			}else{
				$this->L("Common")->ajax_json_error("输入名称已经存在");	
				exit;		
			}
		}		
	}	
	//修改记录	
	public function email_mb_modify (){
		if(empty($_POST)){
			$sql	= "select * from email_mb where id=".$_GET['id'];				 
			$one 	= $this->C($this->cacheDir)->findOne($sql);
			$one["content"]	= ($one["content"]);
			$assArr	=array('one'=>$one);
			$smarty =$this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('email/email_mb_modify.html');
		}else{		
			$content= ($this->_REQUEST("content"));
			$sql="update email_mb set
						name = '$_POST[name]',
						content = '$content'
				  where id=".$_GET['id'];	
			if($this->C($this->cacheDir)->update($sql)>=0){
				$this->L("Common")->ajax_json_success("操作成功",'1','/EmailSend/email_mb_show/');
			}			 
		}			
	}

	//删除记录
	public function email_mb_del (){
		$id	= $this->_REQUEST("ids");
		$sql="delete from email_mb where id in (".$id.")";											 
		if($this->C($this->cacheDir)->update($sql)>0){
			$this->L("Common")->ajax_json_success("操作成功",'1','/EmailSend/email_mb_show/');
		}
		
	}	
	/*********************************************************************
	 * 根据传入的ID值查询出相对的下拉选择框
	 * 
	*/
	public function email_mb_option($inputname,$value=null){
		$sql  	="select * from email_mb";	
		$list 	=$this->C($this->cacheDir)->findAll($sql);
		$string	="<select name='$inputname'  class='combox'>";
		$string.="<option value='0'>选择短信模板</option>";
		foreach($list as $key=>$v){
			$string .="<option value='".$v['id']."'";
			if($v['id']==$value) $string.=" selected";
			$string .=">".$v['name']."</option>";
		}
		$string .="</select>";
		return $string;
	}
	public function email_mb_checkbox($inputname,$value=null){
		$sql  	="select * from email_mb";
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
		$sql  =	"select * from email_mb where id in ($id)";	
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
		$sql  =	"select * from email_mb where id='$id'";	
		$one  =	$this->C($this->cacheDir)->findOne($sql);
		$one["content"]	= ($one["content"]);
		return $one;
	}	

 
/*执行方案*/
	//显示所有
	public function email_scheme(){
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		$where_str = "0=0 ";
		if( !empty($searchValue) ){
			$where_str .=" and $searchKeyword like '%$searchValue%'";
		}
		
		$countSql	= 'select * from email_scheme';
		$totalCount	= $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数	
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql   		= "select * from email_scheme where $where_str order by id desc limit $beginRecord,$numPerPage";		
		$list 		= $this->C($this->cacheDir)->findAll($sql);//查询结果为二维数组，需foreach循
		
		//查询地区
		$area = array();
		if(is_array($list)){
			foreach($list as $key=>$row){
				$mb[$row['id']] = $this->email_mb_get_name($row['contentID']);
				
			}
		}		
		$assignArray=array('list'=>$list,'searchKeyword'=>$searchKeyword,'searchValue'=>$searchValue,
							'mb'=>$mb,
							"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage
						);//适合多个变量注入
		return $assignArray;
	}

	
	public function email_scheme_show(){
		$list	=$this->email_scheme();
		$smarty =$this->setSmarty();
		$smarty->assign($list);
		$smarty->display('email/email_scheme.html'); 
	}	
	//添加记录		
	public function email_scheme_add(){	
		if(empty($_POST)){	
			$recegroup=$this->email_receiver_group_option("receiverID",$value=null);
			$fromgroup=$this->email_from_group_option("fromID",$value=null);
			$mb_check=$this->email_mb_checkbox("mb_box",$value=null);
			$smarty =$this->setSmarty();
			$smarty->assign(array("recegroup"=>$recegroup,"fromgroup"=>$fromgroup,"mb_check"=>$mb_check));
			$smarty->display('email/email_scheme_add.html');     		      
		}else{
			$sql="select name from email_scheme where name='$_POST[name]'";
			$one 	= $this->C($this->cacheDir)->findOne($sql);
			if(empty($one)){
				$now	=date("Y-m-d H:i:s",time());
				$contentID=implode(",",$this->_REQUEST("mb_box"));
				$sql	="insert into email_scheme (name,fromID,receiverID,contentID,intro,adddatetime) 
						  values ('$_POST[name]','$_POST[fromID]','$_POST[receiverID]','$contentID','$_POST[intro]','$now')";		 					
				if($this->C($this->cacheDir)->update($sql)){
					$this->L("Common")->ajax_json_success("操作成功",'1','/EmailSend/email_scheme_show/');
				}			
			}else{
				$this->L("Common")->ajax_json_error("输入名称已经存在");	
				exit;		
			}
		}		
	}	
	//修改记录	
	public function email_scheme_modify (){
		if(empty($_POST)){
			$sql	= "select * from email_scheme where id=".$_GET['id'];				 
			$one 	= $this->C($this->cacheDir)->findOne($sql);
			$recegroup=$this->email_receiver_group_option("receiverID",$value=$one["receiverID"]);
			$fromgroup=$this->email_from_group_option("fromID",$value=$one["fromID"]);
			$mb_check=$this->email_mb_checkbox("mb_box",$value=$one["contentID"]);
			$assArr	=array('one'=>$one,"recegroup"=>$recegroup,"fromgroup"=>$fromgroup,"mb_check"=>$mb_check);
			$smarty =$this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('email/email_scheme_modify.html');
		}else{		
			$contentID=implode(",",$this->_REQUEST("mb_box"));
			$sql="update email_scheme set
						name = '$_POST[name]',
						receiverID = '$_POST[receiverID]',
						fromID = '$_POST[fromID]',
						contentID = '$contentID',
						intro = '$_POST[intro]'
				  where id=".$_GET['id'];
			if($this->C($this->cacheDir)->update($sql)>=0){
				$this->L("Common")->ajax_json_success("操作成功",'1','/EmailSend/email_scheme_show/');
			}			 
		}			
	}

	//删除记录
	public function email_scheme_del (){
		$id	= $this->_REQUEST("ids");
		$sql="delete from email_scheme where id in (".$id.")";											 
		if($this->C($this->cacheDir)->update($sql)>0){
			$this->L("Common")->ajax_json_success("操作成功",'1','/EmailSend/email_scheme_show/');
		}
		
	}	
	/*********************************************************************
	 * 根据传入的ID值查询出相对的下拉选择框
	 * 
	*/
	public function email_scheme_option($inputname,$value=null){
		$sql  	="select * from email_scheme";	
		$list 	=$this->C($this->cacheDir)->findAll($sql);
		$string	="<select name='$inputname'  class='combox'>";
		$string.="<option value='0'>选择短信模板</option>";
		foreach($list as $key=>$v){
			$string .="<option value='".$v['id']."'";
			if($v['id']==$value) $string.=" selected";
			$string .=">".$v['name']."</option>";
		}
		$string .="</select>";
		return $string;
	}
	/*********************************************************************
	 * 根据传入的ID值查询出相对的名称
	 * ex:$id = 1,3,5, 
	*/
	
	public function email_scheme_get_name($id){
		$sql  =	"select * from email_scheme where id in ($id)";	
		$list =	$this->C($this->cacheDir)->findAll($sql);
		$str  =	"";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "".$row["name"]."&nbsp;";
			}
		}
		return $str;
	}

	public function email_scheme_get_one($id){
		$sql  =	"select * from email_scheme where id='$id'";	
		$one  =	$this->C($this->cacheDir)->findOne($sql);
		return $one;
	}
	
	
	/*邮件执行发送*/
	
	public function email_scheme_run(){
		$id    =$this->_REQUEST("id");
		$action=$this->_REQUEST("action");
		$scheme=$this->email_scheme_get_one($id);
		if(!$action){
			$from_sql	= "select * from email_from where groupID='".$scheme["fromID"]."'";
			$from_cnt	= $this->C($this->cacheDir)->countRecords($from_sql);
			$rece_sql	= "select * from email_receiver where groupID='".$scheme["receiverID"]."'";
			$rece_cnt	= $this->C($this->cacheDir)->countRecords($rece_sql);
			$smarty =$this->setSmarty();
			$smarty->assign(array("id"=>$id,"action"=>"send","from_cnt"=>"$from_cnt","rece_cnt"=>"$rece_cnt"));
			$smarty->display('email/email_scheme_run.html');	
		}elseif($action="send"){

			$total	=$this->_REQUEST("total");
			$start	=$this->_REQUEST("start");
			$start  =empty($start)?"1":$start;

			
			$f_sql	= "select * from email_from where groupID='".$scheme["fromID"]."' order by cnt asc limit 0,1";
			$f_rtn  = $this->C($this->cacheDir)->findOne($f_sql);
			
			$r_sql	= "select * from email_receiver where groupID='".$scheme["receiverID"]."' order by cnt asc limit 0,1";
			$r_rtn  = $this->C($this->cacheDir)->findOne($r_sql);

			$m_sql	= "select * from email_mb where id  in (".$scheme["contentID"].") order by cnt asc limit 0,1";
			$m_rtn  = $this->C($this->cacheDir)->findOne($m_sql);	

			$server		=$f_rtn["server"];	
			$port		=$f_rtn["port"];	
			$from		=$f_rtn["account"];	
			$from_pwd	=$f_rtn["password"];
			$from_nam	=$f_rtn["name"];	
			$receiver	=$r_rtn["account"];	
			$subject	=$m_rtn["name"];
			$body		=$m_rtn["content"];	

			
			echo "<br>server=".$server;
			echo "<br>port=".$port;
			echo "<br>from=".$from;
			echo "<br>from_pwd=".$from_pwd;
			echo "<br>receiver=".$receiver;
			echo "<br>subject=".$subject;
			echo "<br>body=".$body;
			echo "<br>server=".$server;
			if($server && $from && $from_pwd && $receiver && $subject && $body){
				$mail =_instance('Extend/MySendMail');
				$mail->setServer($server, $from, $from_pwd,$port);
				$mail->setFrom($from,$from_nam);
				$mail->setReceiver($receiver);
				 //$mail->setReceiver("XXXXX@XXXXX");
		//		$mail->setCc("XXXXX@XXXXX");
		//		$mail->setCc("XXXXX@XXXXX");
		//		$mail->setBcc("XXXXX@XXXXX");
		//		$mail->setBcc("XXXXX@XXXXX");
		//		$mail->setBcc("XXXXX@XXXXX");
				$mail->setMailInfo($subject, $body, $attachment="");
				$rtn=$mail->sendMail();
				if($rtn){
					$status="Yes";
				}else{
					$status="No";	
				}
			}
			
			$this->email_scheme_log_add($id,$from,$receiver,$subject,$status);
			
			//更新发关记录
			$f_u_sql="update email_from set cnt=cnt+1 where id='".$f_rtn["id"]."'";
			$this->C($this->cacheDir)->update($f_u_sql);
			$r_u_sql="update email_receiver set cnt=cnt+1 where id='".$r_rtn["id"]."'";
			$this->C($this->cacheDir)->update($r_u_sql);
			$m_u_sql="update email_mb set cnt=cnt+1 where id='".$m_rtn["id"]."'";
			$this->C($this->cacheDir)->update($m_u_sql);
			
			//设置下一跳
			$next_start	=$start+1;
			if($start<=$total){
				$url	=ACT."/EmailSend/email_scheme_run/id/$id/action/$action/total/$total/start/$next_start/";
				sleep(1);
				echo "<a href='{$url}'>当前执行到第{$start}条记录，如果浏览器没有反应</a>";
				echo "<script language='javascript' type='text/javascript'>";  
				echo "window.location.href='$url'";  
				echo "</script>";	
			}else{
				echo   "<p><a href='#'>方案执行完成,当前执行了{$start}条</a></p>";;			
			}
		}
	}
	
	/*发送邮件跟踪记录*/
	public function email_scheme_log_add($schemeID,$from,$receiver,$subject,$status){
		$now	=date("Y-m-d H:i:s",time());
		$sql	="insert into email_scheme_log (schemeID,sendfrom,receiver,subject,status,adddatetime) 
				  values ('$schemeID','$from','$receiver','$subject','$status','$now')";
		$this->C($this->cacheDir)->update($sql);
	}	
	//显示所有
	public function email_scheme_log(){
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		$where_str = "0=0 ";
		if( !empty($searchValue) ){
			$where_str .=" and $searchKeyword like '%$searchValue%'";
		}
		
		$countSql	= 'select * from email_scheme_log';
		$totalCount	= $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数	
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql   		= "select * from email_scheme_log where $where_str order by id desc limit $beginRecord,$numPerPage";		
		$list 		= $this->C($this->cacheDir)->findAll($sql);//查询结果为二维数组，需foreach循
			
		$assignArray=array('list'=>$list,'searchKeyword'=>$searchKeyword,'searchValue'=>$searchValue,
							"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage
						);//适合多个变量注入
		return $assignArray;
	}

	
	public function email_scheme_log_show(){
		$list	=$this->email_scheme_log();
		$smarty =$this->setSmarty();
		$smarty->assign($list);
		$smarty->display('email/email_scheme_log.html'); 
	}	
	//删除记录
	public function email_scheme_log_del (){
		$id	= $this->_REQUEST("ids");
		$sql="delete from email_scheme_log where id in (".$id.")";											 
		if($this->C($this->cacheDir)->update($sql)>0){
			$this->L("Common")->ajax_json_success("操作成功",'1','/EmailSend/email_scheme_log_show/');
		}
		
	}	
	public function email_send($server,$from,$fromName,$pwd,$rece,$subject,$body,$attachment=null,$port=null){
		$mail = _instance('Extend/MySendMail');
		$mail->setServer($server, $from, $pwd,$port);
		$mail->setFrom($from,$fromName);
		$mail->setReceiver($rece);
		 //$mail->setReceiver("XXXXX@XXXXX");
//		$mail->setCc("XXXXX@XXXXX");
//		$mail->setCc("XXXXX@XXXXX");
//		$mail->setBcc("XXXXX@XXXXX");
//		$mail->setBcc("XXXXX@XXXXX");
//		$mail->setBcc("XXXXX@XXXXX");
		$mail->setMailInfo($subject, $body, $attachment);
		$mail->sendMail();
	}
	
	public function email_send1(){
		$mail = _instance('Extend/MySendMail');
		$mail->setServer("smtp.139.com", "654062906@139.com", "07fly123");
		$mail->setFrom("654062906@139.com","测试html");
		$mail->setReceiver("admin@jz21.net");
		 //$mail->setReceiver("XXXXX@XXXXX");
//		$mail->setCc("XXXXX@XXXXX");
//		$mail->setCc("XXXXX@XXXXX");
//		$mail->setBcc("XXXXX@XXXXX");
//		$mail->setBcc("XXXXX@XXXXX");
//		$mail->setBcc("XXXXX@XXXXX");
$mb=$this->email_mb_get_one(7);
//print_r($mb);
echo $mb["content"];
		$mail->setMailInfo($mb["name"], $mb["content"], $attachment="");
		$mail->sendMail();	
	}
	
	
}//

?>