<?php 
/*
 *
 * sysmanage.Email  邮箱发送记录   
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


class EmailLog extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		// _instance('Action/Auth');
	}		
	//增加短信发送日志
	public function email_log_add($info,$emailaddress,$ipaddr=null){
		$nowtime = date("Y-m-d H:i:s",time());
		$sql 	 = "insert into fly_config_email_log(emailaddress,content,ipaddr,adt) values('$emailaddress','$info','$ipaddr','$nowtime')";
		if($this->C($this->cacheDir)->update($sql)<=0){
			return false;
		}else{
			return true;
		}
	}	
	
	//得到最后一条发送记录
	public function email_log_get_last($emailaddress,$ipaddr){
		$sql= "select * from fly_config_email_log  
						where emailaddress='$emailaddress' or ipaddr='$ipaddr' order by id desc limit 0,1";	
		$one= $this->C($this->cacheDir)->findOne($sql);
		return $one;
			
	}
	
	//获取操作日志
	public function email_log(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;

		//用户查询参数
		$sdt1    = $this->_REQUEST("sdt1");
		$edt1	  = $this->_REQUEST("edt1");	
		$emailaddress   = $this->_REQUEST("emailaddress");	
		$content  = $this->_REQUEST("content");	
		
		$where 		  = "0=0 ";
		if(!empty($content)){
			$where .= " and content like '%$content%'";
		}	
		if($sdt1){
			$where .=" and adt>'$sdt1' ";
		}
		if($edt1){
			$where .=" and adt<='$edt1' ";
		}	
		if($emailaddress){
			$where .=" and emailaddress='$emailaddress' ";
		}				
		$countSql	= "select * from fly_config_email_log where $where";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord= ($pageNum-1)*$pageSize;//计算开始行数
		$sql		= "select * from fly_config_email_log  where $where order by id desc limit $beginRecord,$pageSize";	
		$list		= $this->C($this->cacheDir)->findAll($sql);//查询结果为二维数组，需foreach循环
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);	
		return $assignArray;
	}
	public function email_log_json(){
		$assArr  = $this->email_log();
		echo json_encode($assArr);
	}	
	//调用显示
	public function email_log_show(){
		$smarty = $this->setSmarty();
		$smarty->display('sysmanage/email_log_show.html');			
	}
	
	//批量删除
	public function email_log_del(){
		$ids = $this->_REQUEST("ids");
		$sql = "delete from fly_config_email_log where id in ($ids)";
		$this->C($this->cacheDir)->update($sql);	
		$rtnArr=array('rtnstatus'=>'success','msg'=>'删除成功');
		echo json_encode($rtnArr);
	}
}//end class
?> 