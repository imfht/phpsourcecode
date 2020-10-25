<?php
/*
 *
 * wap.crm.Message  系统消息通知
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
class Message extends Action{
	private $cacheDir='';//缓存目录
	public function __construct() {
		$this->auth=_instance('Action/wap/Auth');
	}	
	public function message(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		//**************************************************************************
		
		//**获得传送来的数据做条件来查询
		$keywords = $this->_REQUEST("keywords");
		$flag = $this->_REQUEST("flag");
		$where_str= "m.owner_user_id = ".SYS_USER_ID."";

		if( !empty($keywords) ){
			$where_str .=" and m.name like '%$keywords%'";
		}	
		//**************************************************************************
		$countSql   = "select m.id from fly_sys_message as m where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($pageNum-1)*$pageSize;
		$sql		 = "select m.* from fly_sys_message as m where $where_str order by m.id desc limit $beginRecord,$pageSize";
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		foreach($list as $key=>$row){
			//$list[$key]['owner_user_arr']	=$this->sys_user->user_get_one($row['owner_user_id']);
			$list[$key]['flag_arr']	=$this->message_flag($row['flag']);
			$list[$key]['url']	=$this->message_url_convert($row['url_type'],$row['url_id']);
		}
		
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum,"statusCode"=>200);	
		return $assignArray;
	}
	public function message_show_json(){
		$assArr = $this->message();
		echo json_encode($assArr);
	}

	//通知查看
	public function message_view(){
		$id	 = $this->_REQUEST("message_id");
		$upt_sql=array('flag'=>'1');
		$this->C($this->cacheDir)->modify('fly_sys_message',$upt_sql,"id='$id'");	
		$sql = "select * from fly_sys_message as m where m.id='$id'";
		$one = $this->C($this->cacheDir)->findOne($sql);	
		$one['flag_arr']	=$this->message_flag($one['flag']);
		$rtnArr =array('one'=>$one,'statusCode'=>'200');
		echo json_encode($rtnArr);
	}
	
	
	//定制调用显示公告
	public function message_noread_cnt(){
		$sql ="select count(*) as cnt from fly_sys_message as u 
			  where flag='-1' and u.owner_user_id = '".SYS_USER_ID."' and (remind_time='0000-00-00 00:00:00' or now()>remind_time) order by create_time desc";
		$one = $this->C($this->cacheDir)->findOne($sql);
		$rtnArr =array('one'=>$one,'statusCode'=>'200');
		echo json_encode($rtnArr);
	}
	public function message_cron_json(){
		$assArr =$this->message_cron();
		echo json_encode($assArr);
	}
	public function message_show_cron(){
		$assArr =$this->message_cron();
		$smarty =$this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('sysmanage/message_show_cron.html');	
	}
	

	//添加
	public function message_add($owner_user_id,$msg_type,$msg_title,$url_type,$url_id,$remind_time=null){
			$into_data=array(
				'msg_type'=>$msg_type,
				'msg_title'=>$msg_title,
				'url_type'=>$url_type,
				'url_id'=>$url_id,
				'remind_time'=>$remind_time,
				'create_time'=>NOWTIME,
				'owner_user_id'=>$owner_user_id,
			);
			$this->C($this->cacheDir)->insert('fly_sys_message',$into_data);
	}		
	
	
	//删除
	public function message_del(){
		$id	  = $this->_REQUEST("message_id");
		$sql  = "delete from fly_sys_message where id in ($id) and id!='1'";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");	
	}	

	//标记已读
	public function message_read(){
		$id	 = $this->_REQUEST("message_id");
		$upt_sql=array('flag'=>'1');
		$this->C($this->cacheDir)->modify('fly_sys_message',$upt_sql,"id in ($id)");		
		$this->L("Common")->ajax_json_success("操作成功");	
	}

	//状态
	public function message_flag($key=null){
		$data=array(
			"-1"=>array(
				 		'flag_name'=>'未读',
				 		'color'=>'#FAD733',
				 		'flag_name_html'=>'<span class="mui-badge mui-badge-primary">未读<span>',
					),
			"1"=>array(
				 		'flag_name'=>'已读',
						'color'=>'#23B7E5',
				 		'flag_name_html'=>'<span class="mui-badge">已读<span>',
					)
		);
		return (array_key_exists($key,$data))?$data[$key]:$data;
	}

	//地址组合
	public function message_url_convert($urt_type=null,$urt_id=null){
		$rtn_url="#";
		switch($urt_type){
			case "sys_user_notice":
				$rtn_url=ACT."/sysmanage/Notice/notice_view/notice_id/".$urt_id."/";
				break;

			case "cst_customer":
				$rtn_url=ACT."/crm/CstCustomer/cst_customer_detail/customer_id/".$urt_id."/";
				break;
			case "cst_trace":
				$rtn_url=ACT."/crm/CstTrace/cst_trace_modify/customer_id/".$urt_id."/";
				break;
			default:
		}
		return $rtn_url;
	}	
	
}//
?>