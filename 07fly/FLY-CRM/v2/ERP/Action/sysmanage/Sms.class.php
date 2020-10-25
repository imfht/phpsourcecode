<?php 
/*
 *
 * sysmanage.SMS  短信发送   
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

class Sms extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		// _instance('Action/Auth');
	}		
	//系统常规设置
	public function sms_config(){
		if(empty($_POST)){
			$config 		= $this->get_sms_info();
			$smarty 		= $this->setSmarty();
			$smarty->assign(array("one"=>$config));
			$smarty->display('sysmanage/sms_config.html');					
		}else{			
			foreach($_POST as $key=>$v){
				$sql="INSERT INTO fly_config_sms(name,value) VALUES('$key','$v') 
						ON DUPLICATE KEY UPDATE value='$v'";
				$this->C($this->cacheDir)->update($sql);
			}
			$this->location("操作成功","/sysmanage/Sms/sms_config/");
		}
	}	
	//得到配置参数
	public function get_sms_info(){
		$sql 	= "select * from fly_config_sms;";
		$list	= $this->C($this->cacheDir)->findAll($sql);
		$assArr = array();
		if(is_array($list)){
			foreach($list as $key=>$row){
				$assArr[$row["name"]] = $row["value"];
			}
		}
		return $assArr;		
	}	
	
	//短信对外提供的发送接口
	//
	public function sms_send_api($mobile,$tagsArr,$mb_id=null){
		if($mobile){
			$rtn=$this->sms_send($mobile,$tagsArr,$mb_id);
			if($rtn){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
		
	}
	
	//短信发送接口
	public function sms_send($mobile,$tagsArr,$mb_id=null){
		$sms_conf	=$this->get_sms_info();
		if($mb_id){
			$sql="select * from fly_config_sms_mb where id='$mb_id'";
			$one=$this->C($this->cacheDir)->findOne($sql);
			$content=$one["content"];
			$tpl_id =$one["tpl_id"];
		}else{
			$content=$sms_conf["content"];
			$tpl_id =$sms_conf["tpl_id"];
		}
		$content=$this->L("Common")->replace_tags($tagsArr,$content);
		$sms_content=explode(",",$content);
		
		
		//腾讯接口
		$result	=$this->L("TencentSms")->sms_send($sms_conf,$mobile,$sms_content,$tpl_id);
		$result	=json_decode($result,true);
		if(strstr($result["result"], '0') !== false){
			$this->sms_log_add($content,$mobile,$ipaddr=null);
			return true;
		}else{
			return false;
		}	
	}
	
	//短信发送记录验证
	public function sms_send_check($mobile,$sms_content){
		$sql="select * from fly_config_sms_log 
			where mobile='$mobile' and content like '%$sms_content%'";
		$one=$this->C($this->cacheDir)->findAll($sql);
		if($one){
			return true;
		}else{
			return false;
		}
	}	

}//end class
?> 