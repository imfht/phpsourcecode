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

class WorkLogModel extends CommonModel {
	// 自动验证设置
	
	function _after_insert($data, $options) {

		$where['dept_id'] = array('eq',get_dept_id());
		$dept_user_list = M('User') -> where($where) -> getField('id', true);

		foreach ($dept_user_list as $val) {
			$auth = D("Role") -> get_auth('WorkLog', $val);
			if ($auth['admin']) {
				$user_list[] = $val;
			}
		}
		
		$push_data['type'] = '日报';
		$push_data['action'] = '需要查阅';
		$push_data['title'] = "来自：" . get_dept_name() . "-" . get_user_name();
		$push_data['content'] = "工作日报：" . $data['start_date']."-".$data['end_date'];
		$user_id=get_user_id();
		$push_data['url'] = U('WorkLog/index',"eq_user_id={$user_id}&return_url=WorkLog/index");
		
		send_push($push_data, $user_list);
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
				M("TaskLog") -> add($log_data);
			}
		}
	}

	function _send_mail($task_id, $executor) {
		$executor_info = M("User") -> where("id=$executor") -> find();

		$email = $executor_info['email'];
		$user_name = $executor_info['name'];

		$info = M("Task") -> where("id=$task_id") -> find();

		$title = "您有新的任务：" . $info['name'];

		$body = "您好，{$user_name}，{$info['user_name']} 有一个任务需要您的协助！</br>";
		$body .= "任务主题：{$info['name']}</br>";
		$body .= "任务时间：{$info['expected_time']}</br>";
		$body .= "任务发起人：{$info['user_name']}</br>";
		$body .= "请与{$info['user_name']}做好沟通，尽快完成任务。</br>";
		$body .= "点击查看任务详情：http://" . $_SERVER['SERVER_NAME'] . U('Task/read', 'id=' . $info['id']) . "</br>";
		$body .= "霞湖世家，感谢有您！</br>";

		send_mail($email, $user_name, $title, $body);
	}

}
?>