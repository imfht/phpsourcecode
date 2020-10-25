<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 --------------------------------------------------------------*/
namespace Home\Controller;

class WorkOrderController extends HomeController {
	protected $config = array('app_type' => 'common', 'read' => 'let_me_do,accept,reject,save_log,sign,finish,edit2', 'admin' => 'report');

	//过滤查询字段
	function _search_filter(&$map) {
		$map['is_del'] = array('eq', '0');
		if (!empty($_REQUEST['keyword']) && empty($map['64'])) {
			$map['executor'] = array('like', "%" . $_POST['keyword'] . "%");
		}
	}

	public function index() {
		$this -> redirect('folder', array('fid' => 'todo'));
	}

	public function folder() {
		$plugin['date'] = true;
		$this -> assign("plugin", $plugin);
		$this -> assign('auth', $this -> config['auth']);
		$this -> assign('user_id', get_user_id());

		$where = $this -> _search();
		if (method_exists($this, '_search_filter')) {
			$this -> _search_filter($where);
		}

		$todo_work_order_count = badge_count_todo_work_order();
		$this -> assign('todo_work_order_count', $todo_work_order_count);

		$doing_work_order_count = badge_count_doing_work_order();
		$this -> assign('doing_work_order_count', $doing_work_order_count);

		$fid = $_GET['fid'];
		$this -> assign("fid", $fid);

		switch ($fid) {
			case 'all' :
				$this -> assign("folder_name", '所有任务');
				break;
			case 'todo' :
				$this -> assign("folder_name", '我未接受的任务');

				$where_log['type'] = 1;
				$where_log['status'] = 0;
				$where_log['executor'] = get_user_id();
				$task_list = M("WorkOrderLog") -> where($where_log) -> getField('task_id', TRUE);
				if (empty($task_list)) {
					$where['_string'] = '1=2';
				} else {
					$where['id'] = array('in', $task_list);
				}
				break;

			case 'my_no_finish' :
				$this -> assign("folder_name", '我已接受的任务');
				$where_log['status'] = array('in', '1,2');
				$where_log['executor'] = get_user_id();
				$where_log['type'] = array('eq', 1);

				$task_list = M("WorkOrderLog") -> where($where_log) -> getField('task_id', true);
				if (empty($task_list)) {
					$where['_string'] = '1=2';
				} else {
					$where['id'] = array('in', $task_list);
				}

				break;
				
			case 'no_finish2' :
				$this -> assign("folder_name", '所有任务-未完成');

				$where_log['status'] = array('lt', 2);
				$where_log['type'] = array('eq', 1);

				$task_list = M("WorkOrderLog") -> where($where_log) -> getField('task_id', true);
				if (empty($task_list)) {
					$where['_string'] = '1=2';
				} else {
					$where['id'] = array('in', $task_list);
				}

				break;
				
			case 'finished' :
				$this -> assign("folder_name", '所有任务-已完成');
				$where_log['type'] = array('eq', 1);

				$task_list = M("WorkOrderLog") -> where($where_log) -> getField('task_id', true);
				if (empty($task_list)) {
					$where['_string'] = '1=2';
				} else {
					$where['id'] = array('in', $task_list);
					$where['status'] = array('eq', 3);
				}
				break;
				
			case 'my_task' :
				$this -> assign("folder_name", '我发布的任务');
				$where['user_id'] = get_user_id();
				break;
				
			case 'my_assign' :
				$this -> assign("folder_name", '我指派的任务');

				$where_log['assigner'] = get_user_id();
				$task_list = M("WorkOrderLog") -> where($where_log) -> getField('task_id', TRUE);
				if (empty($task_list)) {
					$where['_string'] = '1=2';
				} else {
					$where['id'] = array('in', $task_list);
				}
				break;
				
			default :
				break;
		}

		$model = D('WorkOrder');
		if (!empty($model)) {
			$this -> _list($model, $where);
		}
		$this -> display();
	}

	public function report() {
		$offset = I('offset');

		if (empty($offset)) {
			$offset = 0;
		}

		$this -> assign('next_offset', $offset + 1);
		$this -> assign('prev_offset', $offset - 1);
		$offset_val = 7 * $offset;

		$start_date = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") - date("w") + 1 + $offset_val, date("Y")));
		$end_date = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d") - date("w") + 7 + $offset_val, date("Y")));

		$day_1 = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - date("w") + 1 + $offset_val, date("Y")));
		$day_2 = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - date("w") + 2 + $offset_val, date("Y")));
		$day_3 = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - date("w") + 3 + $offset_val, date("Y")));
		$day_4 = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - date("w") + 4 + $offset_val, date("Y")));
		$day_5 = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - date("w") + 5 + $offset_val, date("Y")));
		$day_6 = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - date("w") + 6 + $offset_val, date("Y")));
		$day_7 = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - date("w") + 7 + $offset_val, date("Y")));

		$this -> assign('day_1', $day_1);
		$this -> assign('day_2', $day_2);
		$this -> assign('day_3', $day_3);
		$this -> assign('day_4', $day_4);
		$this -> assign('day_5', $day_5);
		$this -> assign('day_6', $day_6);
		$this -> assign('day_7', $day_7);

		$where['request_arrive_time'] = array( array('gt', $start_date), array('lt', $end_date));
		$where['is_del'] = array('eq', 0);
		$where['status'] = array('eq', 3);
		$list = D('WorkOrderLogView') -> where($where) -> getField('executor,executor_name');

		$this -> assign('list', $list);
		$this -> display();
	}

	public function edit($id){
		$plugin['uploader'] = true;
		$plugin['editor'] = true;
		$plugin['date'] = true;
		$this -> assign("plugin", $plugin);

		$customer_list = M("Customer") -> where('is_del=0') -> getField("short id,short");
		$this -> assign('customer_list', $customer_list);

		$model = M("WorkOrder");
		$this -> _edit($id);
	}

	public function edit2($id){
		$this->edit($id);
	}
	
	public function del($id) {
		$this -> _del($id);
	}

	public function add() {
		$plugin['uploader'] = true;
		$plugin['editor'] = true;
		$plugin['date'] = true;
		$this -> assign("plugin", $plugin);

		$customer_list = M("Customer") -> where('is_del=0') -> getField("short id,short");
		$this -> assign('customer_list', $customer_list);
		$fid = array();
		$this -> assign('folder', $fid);
		$this -> display();
	}

	public function read($id) {		
		vendor('WeiXin.jssdk');
		$corp_id = get_system_config('weixin_corp_id');
		$secret = get_system_config('weixin_secret');
		$jssdk = new \JSSDK($corp_id, $secret);
		$signPackage = $jssdk -> GetSignPackage();

		$this -> assign('signPackage', $signPackage);
		$this -> assign('is_weixin', is_weixin());
		$this -> assign('task_id', $id);
		
		$model = M("WorkOrder");
		$vo = $model -> find($id);

		$this -> assign('vo', $vo);

		$where_log['task_id'] = array('eq', $id);
		$task_log = M("WorkOrderLog") -> where($where_log) -> select();

		$this -> assign('task_log', $task_log);
		$where_accept['status'] = 0;
		$where_accept['task_id'] = $id;
		$where_accept['type'] = 1;
		$where_accept['executor'] = array('eq', get_user_id());
		$task_accept = M("WorkOrderLog") -> where($where_accept) -> find();

		if (!empty($task_accept)) {
			$this -> assign('is_accept', 1);
			$this -> assign('task_log_id', $task_accept['id']);
		} else {
			$this -> assign('is_accept', 0);
		}
		
		$where_working['status'] = array('in', '1,2');
		$where_working['task_id'] = $id;
		$where_working['transactor'] = array('eq', get_user_id());
		$task_working = M("WorkOrderLog") -> where($where_working) -> find();

		if ($task_working) {
			$this -> assign('is_working', 1);
			$this -> assign('task_working', $task_working);
			$this -> assign('task_log_id', $task_working['id']);
		}
		$this -> display();
	}

	function accept() {
		if (IS_POST) {
			$task_log_id = I('task_log_id');
			$data['id'] = $task_log_id;
			$data['transactor'] = get_user_id();
			$data['transactor_name'] = get_user_name();
			$data['status'] = 1;
			$list = M("WorkOrderLog") -> save($data);

			$task_id = M("WorkOrderLog") -> where("id=$task_log_id") -> getField('task_id');
			M("WorkOrder") -> where("id=$task_id") -> setField('status', 1);

			if ($list != false) {
				$this -> _add_to_schedule($task_id);
				$return['info'] = '接受成功';
				$return['status'] = 1;
				$this -> ajaxReturn($return);
			} else {
				$this -> error('提交成功');
			}
		}
	}

	function sign() {
		if (IS_POST) {
			$task_log = M("WorkOrderLog") -> find(I('task_log_id'));
			$arrive_time = time();
			$task_log['arrive_time'] = $arrive_time;
			$task_log['status'] = 2;
			$task_log['arrive_lat'] = I('lat');
			$task_log['arrive_lng'] = I('lng');
			$task_log['arrive_location'] = get_location(I('lat'), I('lng'));
			$list = M("WorkOrderLog") -> save($task_log);

			if ($list != false) {
				if ($task_log['type'] == 1) {
					$task['id'] = $task_log['task_id'];
					$task['arrive_time'] = $arrive_time;
					$task['arrive_lat'] = $task_log['arrive_lat'];
					$task['arrive_lng'] = $task_log['arrive_lng'];
					$task['arrive_location'] = $task_log['arrive_location'];
					$task['status'] = 3;
					M("WorkOrder") -> save($task);
				}
				$return['info'] = '签到成功';
				$return['status'] = 1;
				$this -> ajaxReturn($return);
			} else {
				$this -> error('签到失败');
			}
		}
	}

	function finish() {
		if (IS_POST) {
			$task_log = M("WorkOrderLog") -> find(I('task_log_id'));
			$finish_time = time();
			$task_log['finish_time'] = $finish_time;
			$task_log['status'] = 3;
			$task_log['finish_lat'] = I('lat');
			$task_log['finish_lng'] = I('lng');
			$task_log['finish_location'] = get_location(I('lat'), I('lng'));
			$list = M("WorkOrderLog") -> save($task_log);

			if ($list != false) {
				if ($task_log['type'] == 1) {
					$task['id'] = $task_log['task_id'];
					$task['finish_time'] = $finish_time;
					$task['finish_lat'] = $task_log['finish_lat'];
					$task['finish_lng'] = $task_log['finish_lng'];
					$task['finish_location'] = $task_log['finish_location'];
					$task['status'] = 3;
					M("WorkOrder") -> save($task);
				}
				$return['info'] = '提交成功';
				$return['status'] = 1;
				$this -> ajaxReturn($return);
			} else {
				$this -> error('提交失败');
			}
		}
	}

	function reject() {
		$plugin['editor'] = true;
		$this -> assign("plugin", $plugin);
		if (IS_POST) {
			$model = D("WorkOrderLog");
			if (false === $model -> create()) {
				$this -> error($model -> getError());
			}
			$model -> transactor = get_user_id();
			$model -> transactor_name = get_user_name();
			$model -> finish_time = to_date(time());
			$list = $model -> save();
			$status = I('status');
			$task_id = I('task_id');
			if ($status > 2) {
				$where_count['task_id'] = array('eq', $task_id);
				$total_count = M("WorkOrderLog") -> where($where_count) -> count();

				$where_count['status'] = array('gt', 2);
				$finish_count = M("WorkOrderLog") -> where($where_count) -> count();
				if ($total_count == $finish_count) {
					M("WorkOrder") -> where("id=$task_id") -> setField('status', 5);
					$user_id = M('WorkOrder') -> where("id=$task_id") -> getField('user_id');
					//$this -> _send_mail_finish($task_id, $user_id);
				}
			}
			if ($list !== false) {
				$this -> success('提交成功');
			} else {
				$this -> success('提交失败');
			}
		}

		$task_id = I('task_id');
		$where_log1['type'] = 1;
		$where_log1['executor'] = get_user_id();
		$where_log1['task_id'] = $task_id;
		$task_log1 = M("WorkOrderLog") -> where($where_log1) -> find();
		if ($task_log1) {
			$this -> assign('task_log', $task_log1);
		} else {
			$where_log2['type'] = 2;
			$where_log2['executor'] = get_dept_id();
			$where_log2['task_id'] = $task_id;
			$task_log2 = M("WorkOrderLog") -> where($where_log2) -> find();

			if ($task_log2) {
				$this -> assign('task_log', $task_log2);
			}
		}
		$this -> display();
	}

	function upload() {
		$this -> _upload();
	}

	function down($attach_id) {
		$this -> _down($attach_id);
	}

	private function _add_to_schedule($task_id) {
		$info = M("WorkOrder") -> where("id=$task_id") -> find();
		$data['name'] = $info['name'];
		$data['content'] = $info['content'];
		$data['start_time'] = to_date(time());
		$data['end_time'] = $info['expected_time'];
		$data['user_id'] = get_user_id();
		$data['user_name'] = get_user_name();
		$data['priority'] = 3;

		$list = M('Schedule') -> add($data);
	}

}
