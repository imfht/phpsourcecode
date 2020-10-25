<?php 
/*
 *
 * sysmanage.SmsMb  短信模板   
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

class SmsMb extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		// _instance('Action/Auth');
	}		

	//显示所有
	public function sms_mb(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		//**************************************************************************
		
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		$where_str = "0=0 ";
		if( !empty($searchValue) ){
			$where_str .=" and $searchKeyword like '%$searchValue%'";
		}
		
		$countSql	= 'select * from fly_config_sms_mb';
		$totalCount	= $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数	
		$beginRecord= ($pageNum-1)*$pageSize;
		$sql   	  = "select * from fly_config_sms_mb where $where_str order by id desc limit $beginRecord,$pageSize";		
		$list 		= $this->C($this->cacheDir)->findAll($sql);//查询结果为二维数组，需foreach循
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);
		return $assignArray;
	}
	
	public function sms_mb_json(){
		$list	=$this->sms_mb();
		echo json_encode($list);
	}
	
	public function sms_mb_show(){
		$list	=$this->sms_mb();
		$smarty =$this->setSmarty();
		$smarty->assign($list);
		$smarty->display('sysmanage/sms_mb_show.html'); 
	}	
	//添加记录		
	public function sms_mb_add(){	
		if(empty($_POST)){	
			$smarty =$this->setSmarty();
			$smarty->display('sysmanage/sms_mb_add.html');     		      
		}else{
			$name	= $this->_REQUEST("name");
			$content= $this->_REQUEST("content");
			$tpl_id	= $this->_REQUEST("tpl_id");
			$sql="select name from fly_config_sms_mb where tpl_id='$tpl_id'";
			$one 	= $this->C($this->cacheDir)->findOne($sql);
			if(empty($one)){
				$now	=date("Y-m-d H:i:s",time());
				$sql	="insert into fly_config_sms_mb (name,tpl_id,content,adddatetime) 
								  values ('$name','$tpl_id','$content','$now')";		 					
				if($this->C($this->cacheDir)->update($sql)){
					$this->location("操作成功",'/sysmanage/SmsMb/sms_mb_show/');
				}			
			}else{
				$this->location("模板编号已经存在",'/sysmanage/SmsMb/sms_mb_show/','1');
				exit;		
			}
		}		
	}	
	//修改记录	
	public function sms_mb_modify (){
		$id	= $this->_REQUEST("id");
		if(empty($_POST)){
			$sql	= "select * from fly_config_sms_mb where id='$id'";				 
			$one 	= $this->C($this->cacheDir)->findOne($sql);
			$assArr	=array('one'=>$one);
			$smarty =$this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('sysmanage/sms_mb_modify.html');
		}else{		
			$name	= $this->_REQUEST("name");
			$content= $this->_REQUEST("content");
			$tpl_id	= $this->_REQUEST("tpl_id");
			$sql="update fly_config_sms_mb set
						name = '$name',
						tpl_id = '$tpl_id',
						content = '$content'
				  where id=".$_GET['id'];	
			if($this->C($this->cacheDir)->update($sql)>=0){
				$this->location("操作成功",'/sysmanage/SmsMb/sms_mb_show/');
			}			 
		}			
	}

	//删除记录
	public function sms_mb_del (){
		$id	= $this->_REQUEST("ids");
		$sql="delete from fly_config_sms_mb where id in (".$id.")";											 
		if($this->C($this->cacheDir)->update($sql)>0){
			$this->L("Common")->ajax_json_success("操作成功",'1','/Sms/sms_mb_show/');
		}
		
	}	
	/*********************************************************************
	 * 根据传入的ID值查询出相对的下拉选择框
	 * 
	*/
	public function sms_mb_option($inputname,$value=null){
		$sql  	="select * from fly_config_sms_mb";	
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
	
	public function sms_mb_get_name($id){
		$sql  =	"select * from fly_config_sms_mb where id in ($id)";	
		$list =	$this->C($this->cacheDir)->findAll($sql);
		$str  =	"";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "".$row["name"]."&nbsp;";
			}
		}
		return $str;
	}

	public function sms_mb_get_one($id){
		$sql  =	"select * from fly_config_sms_mb where id='$id'";	
		$one  =	$this->C($this->cacheDir)->findOne($sql);
		return $one;
	}	

	
}//end class
?> 