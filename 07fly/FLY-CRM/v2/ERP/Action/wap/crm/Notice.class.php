<?php
/*
 *
 * sysmanage.Notice  系统公告通知   
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
class Notice extends Action{
	private $cacheDir='';//缓存目录
	public function __construct() {
		$this->auth=_instance('Action/wap/Auth');
	}	
	public function notice(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		//**************************************************************************
		
		//**获得传送来的数据做条件来查询
		$keywords = $this->_REQUEST("keywords");
		$status = $this->_REQUEST("status");
		$where_str= "n.owner_user_id = ".SYS_USER_ID."";

		if( !empty($keywords) ){
			$where_str .=" and n.name like '%$keywords%'";
		}	
		//**************************************************************************
		$countSql   = "select n.id from fly_sys_user_notice as n where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($pageNum-1)*$pageSize;
		$sql		 = "select n.*,u.name as create_user_name from fly_sys_user_notice as n left join fly_sys_user as u on n.create_user_id=u.id 
						where $where_str order by n.id desc limit $beginRecord,$pageSize";
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		foreach($list as $key=>$row){
			//$list[$key]['owner_user_arr']	=$this->sys_user->user_get_one($row['owner_user_id']);
			$list[$key]['status_arr']	=$this->notice_status($row['status']);
		}
		
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum,"statusCode"=>'200');	
		return $assignArray;
	}
	public function notice_show_json(){
		$assArr = $this->notice();
		echo json_encode($assArr);
	}
	public function notice_show(){
			$smarty = $this->setSmarty();
			$smarty->display('sysmanage/notice_show.html');	
	}	
	//定制调用显示公告
	public function notice_noread_cnt(){
		$sql ="select count(*) as cnt from fly_sys_user_notice as u where status='-1' and u.owner_user_id = '".SYS_USER_ID."' order by create_time desc";
		$one = $this->C($this->cacheDir)->findOne($sql);
		$rtnArr =array('one'=>$one,'statusCode'=>'200');
		echo json_encode($rtnArr);
	}
	

	//添加
	public function notice_add(){
		if(empty($_POST)){
			$role_opt=$this->sys_role->role_select_tree('role_id');
			$sys_user=$this->sys_user->user_list();
			$smarty = $this->setSmarty();
			$smarty->assign(array("role_opt"=>$role_opt,"sys_user"=>$sys_user));
			$smarty->display('sysmanage/notice_add.html');	
		}else{
			$role_id = $this->_REQUEST("role_id");
			$user_id = $this->_REQUEST("sys_user_id");
			if($role_id){
				$role_son_id=$this->sys_role->role_all_child($role_id);//得到权限子级
				$role_son_id[]=$role_id;//加上选择权限本身
				$role_son_user=$this->sys_user->user_list_role($role_son_id);
				$sub_user=array();		
				foreach($role_son_user as $row){
					$sub_user[]=$row['id'];
				}
			}else if(!empty($user_id)){
				$sub_user[]=$user_id;
			}else{
				$sub_user=$this->sys_user->user_get_sub_user(SYS_USER_ID);//得到当前用户下级员工数据
			}
			//对每个用户发送通知
			foreach($sub_user as $sub_user_id){
				$into_data=array(
					'title'=>$this->_REQUEST("title"),
					'content'=>$this->_REQUEST("content"),
					'owner_user_id'=>$sub_user_id,
					'create_time'=>NOWTIME,
					'create_user_id'=>SYS_USER_ID,
				);
				$rtn_id=$this->C($this->cacheDir)->insert('fly_sys_user_notice',$into_data);	
				
				$this->msg->message_add($sub_user_id,'公告通知',$this->_REQUEST("title"),'sys_user_notice',$rtn_id,$remind_time=null);
				
			}
			$this->L("Common")->ajax_json_success("操作成功");	
		}
	}		
	
	//通知查看
	public function notice_view(){
		$id	 = $this->_REQUEST("notice_id");
		$upt_sql=array('status'=>'1');
		$this->C($this->cacheDir)->modify('fly_sys_user_notice',$upt_sql,"id='$id'");	
		$sql = "select n.*,u.name as create_user_name from fly_sys_user_notice as n left join fly_sys_user as u on n.create_user_id=u.id where n.id='$id'";
		$one = $this->C($this->cacheDir)->findOne($sql);	
		$one['status_arr']	=$this->notice_status($one['status']);
		$rtnArr =array('one'=>$one,'statusCode'=>'200');
		echo json_encode($rtnArr);
	}
	
	//删除
	public function notice_del(){
		$id	  = $this->_REQUEST("notice_id");
		$sql  = "delete from fly_sys_user_notice where id in ($id) and id!='1'";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");	
	}	

	//标记已读
	public function notice_read(){
		$id	 = $this->_REQUEST("notice_id");
		$upt_sql=array('status'=>'1');
		$this->C($this->cacheDir)->modify('fly_sys_user_notice',$upt_sql,"id in ($id)");		
		$this->L("Common")->ajax_json_success("操作成功");	
	}

	//状态
	public function notice_status($key=null){
		$data=array(
			"-1"=>array(
				 		'status_name'=>'未查看',
				 		'color'=>'#FAD733',
				 		'status_name_html'=>'<span class="mui-badge mui-badge-primary">未查看<span>',
					),
			"1"=>array(
				 		'status_name'=>'已查看',
						'color'=>'#23B7E5',
				 		'status_name_html'=>'<span class="mui-badge">已查看<span>',
					)
		);
		return ($key)?$data[$key]:$data;
	}
	
}//
?>