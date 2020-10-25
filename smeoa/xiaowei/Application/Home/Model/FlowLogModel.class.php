<?php
/*---------------------------------------------------------------------------
  小微OA系统 - 让工作更轻松快乐 

  Copyright (c) 2013 http://www.smeoa.com All rights reserved.                                             

  Author:  jinzhu.yin<smeoa@qq.com>                         

  Support: https://git.oschina.net/smeoa/xiaowei               
 -------------------------------------------------------------------------*/


namespace Home\Model;
use Think\Model;

class  FlowLogModel extends CommonModel {
	
	function _before_insert(&$data,$options){
		$emp_no = $data["emp_no"];
		$where['emp_no']=array('eq',$emp_no);
		$user_id=M("User")->where($where)->getField("id");
		$user_name=M("User")->where($where)->getField("name");
		$data["user_id"]=$user_id;
		$data["user_name"]=$user_name;
		
		$flow = M("Flow") -> find($data['flow_id']);
		
		if($data['step']!=100){
			$push_data['type'] = '审批';
			$push_data['action'] = '需要审批';
			$push_data['title'] = $flow['name'];
			$push_data['content'] = '提交人：'.get_dept_name()."-".get_user_name();
			$push_data['url'] = U('Flow/read',"id={$data['flow_id']}&return_url=Flow/index");
			send_push($push_data,$user_id);	
		}
	}
}
?>