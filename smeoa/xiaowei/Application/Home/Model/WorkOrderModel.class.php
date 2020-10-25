<?php
/*---------------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 -------------------------------------------------------------------------*/
// 用户模型
namespace Home\Model;
use Think\Model;

class WorkOrderModel extends CommonModel {
	// 自动验证设置
	protected $_validate = array( array('name', 'require', '文件名必须', 1), array('content', 'require', '内容必须'), );

	function _before_insert(&$data, $options) {
		if ($data['name'] == "#") {
			$data['name'] = $data['other'];
		}
		$sql = "SELECT CONCAT(year(now()),'-',LPAD(count(*)+1,4,0)) task_no FROM `" . $this -> tablePrefix . "task` WHERE 1 and year(FROM_UNIXTIME(create_time))>=year(now())";
		$rs = $this -> db -> query($sql);
		if ($rs) {
			$data['task_no'] = $rs[0]['task_no'];
		} else {
			$data['task_no'] = date('Y') . "-0001";
		}
		$data['status']=1;
	}

	function _after_insert($data, $options) {

		$executor_list = $data['executor'];
		$executor_list = array_filter(explode(';', $executor_list));

		if (!empty($executor_list)) {
			$user_list=array();
			foreach ($executor_list as $key => $val) {
				$tmp = explode('|', $val);
				$executor_name = $tmp[0];
				$executor = $tmp[1];
				$type = 1;

				$user_list[] = $executor;

				$log_data['executor'] = $executor;
				$log_data['executor_name'] = $executor_name;
				
				$log_data['transactor'] = $executor;
				$log_data['transactor_name'] = $executor;
				
				$log_data['type'] = $type;
				$log_data['status']=1;
				$log_data['assigner'] = $data['user_id'];
				$log_data['task_id'] = $data['id'];
				
				M("WorkOrderLog") -> add($log_data);
			}
			
			$push_data['type'] = '工单';
			$push_data['action'] = '需要执行';
			$push_data['title'] = "来自：" . $data['user_name'];
			$push_data['content'] = "客户：{$data['name']}</br>
			要求到达时间：{$data['request_arrive_time']}";
			$push_data['url'] = U('WorkOrder/read',"id={$data['id']}&return_url=WorkOrder/index");

			send_push($push_data, $user_list);
		}

		$executor_list = $data['actor'];
		$executor_list = array_filter(explode(';', $executor_list));
		
		if (!empty($executor_list)) {
			$user_list = array();
			foreach ($executor_list as $key => $val) {
				$tmp = explode('|', $val);
				$executor_name = $tmp[0];
				$executor = $tmp[1];

				$type = 2;
				$user_list[] = $executor;

				$log_data['executor'] = $executor;
				$log_data['executor_name'] = $executor_name;
				$log_data['type'] = $type;
				$log_data['status']=1;
				$log_data['assigner'] = $data['user_id'];
				$log_data['task_id'] = $data['id'];
				M("WorkOrderLog") -> add($log_data);
			}
			$push_data['type'] = '工单';
			$push_data['action'] = '需要参与';
			$push_data['title'] = "来自：" . $data['user_name'];
			$push_data['content'] = "客户：{$data['name']}</br>
			要求到达时间：{$data['request_arrive_time']}";
			$push_data['url'] = U('WorkOrder/read',"id={$data['id']}&return_url=WorkOrder/index");
			
			send_push($push_data, $user_list);
		}
	}

	function forword($task_id, $executor_list) {
		$executor_list = array_filter(explode(';', $executor_list));

		if (!empty($executor_list)) {
			foreach ($executor_list as $key => $val) {
				$tmp = explode('|', $val);
				$executor_name = $tmp[0];
				$executor = $tmp[1];

				if (strpos($executor, "dept_") !== false) {
					$type = 2;
					$executor = str_replace('dept_', '', $executor);
				} else {
					$type = 1;
				}

				$log_data['executor'] = $executor;
				$log_data['executor_name'] = $executor_name;
				$log_data['type'] = $type;
				$log_data['assigner'] = get_user_id();
				$log_data['task_id'] = $task_id;
				M("WorkOrderLog") -> add($log_data);
			}
		}
	}

}
?>