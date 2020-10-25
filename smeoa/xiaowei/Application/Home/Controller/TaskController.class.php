<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 --------------------------------------------------------------*/
namespace Home\Controller;

class TaskController extends HomeController {
	protected $config = array('app_type' => 'common', 'read' => 'let_me_do,accept,reject,save_log');

	//过滤查询字段
	function _search_filter(&$map) {
		$map['is_del'] = array('eq', '0');
		if (!empty($_REQUEST['keyword']) && empty($map['64'])) {
			$map['name'] = array('like', "%" . $_POST['keyword'] . "%");
		}
	}

	public function index() {
		$this -> redirect('folder', array('fid' => 'all'));
	}

	/*--------------------------------------------------------------------
	 * 任务状态说明
	 * 0:未处理
	 * 10:进行中
	 * 20:已完成
	 * 21:已转交
	 * 22:已拒绝
	 --------------------------------------------------------------------*/
	public function folder() {
		$plugin['date'] = true;
		$this -> assign("plugin", $plugin);
		$this -> assign('auth', $this -> config['auth']);
		$this -> assign('user_id', get_user_id());

		$where = $this -> _search();
		if (method_exists($this, '_search_filter')) {
			$this -> _search_filter($where);
		}

		$no_finish_task_count = badge_count_no_finish_task();
		$dept_task_count = badge_count_dept_task();
		$no_assign_task_count = badge_count_no_assign_task();

		$this -> assign('no_finish_task_count', $no_finish_task_count);
		$this -> assign('dept_task_count', $dept_task_count);
		$this -> assign('no_assign_task_count', $no_assign_task_count);

		$fid = $_GET['fid'];
		$this -> assign("fid", $fid);

		switch ($fid) {
			case 'all' :
				$this -> assign("folder_name", '所有任务');
				break;
				
			case 'dept' :
				$this -> assign("folder_name", '我们部门的任务');
				$auth = $this -> config['auth'];

				if ($auth['admin']) {
					$where_log['type'] = 2;
					$where_log['status'] = array('eq', '0');
					$where_log['executor'] = get_dept_id();
					$task_list = M("TaskLog") -> where($where_log) -> getField('task_id', TRUE);
					if (empty($task_list)) {
						$where['_string'] = '1=2';
					} else {
						$where['id'] = array('in', $task_list);
					}
				} else {
					$where['_string'] = '1=2';
				}
				break;

			case 'no_assign' :
				$this -> assign("folder_name", '不知道由谁处理的任务');

				$prefix = C('DB_PREFIX');

				$assign_list = M("Task") -> getField('id', true);

				$sql = "select id from {$prefix}task task where status=0 and not exists (select * from {$prefix}task_log task_log where task.id=task_log.task_id)";
				$task_list = M() -> query($sql);

				if (empty($task_list)) {
					$where['_string'] = '1=2';
				} else {
					foreach ($task_list as $key => $val) {
						$list[] = $val['id'];
					}
					$where['id'] = array('in', $list);
				}

				break;

			case 'no_finish' :
				$this -> assign("folder_name", '我未完成');

				$where_log['status'] = array('lt', 20);
				$where_log['executor'] = get_user_id();
				$where_log['type'] = array('eq', 1);

				$task_list = M("TaskLog") -> where($where_log) -> getField('task_id', true);
				if (empty($task_list)) {
					$where['_string'] = '1=2';
				} else {
					$where['id'] = array('in', $task_list);
				}

				break;

			case 'finished' :
				$this -> assign("folder_name", '我已完成');

				$where_log['executor'] = get_user_id();
				$where_log['type'] = array('eq', 1);

				$task_list = M("TaskLog") -> where($where_log) -> getField('task_id', true);
				if (empty($task_list)) {
					$where['_string'] = '1=2';
				} else {
					$where['id'] = array('in', $task_list);
					$where['status'] = array('eq', 30);
				}
				break;

			case 'my_task' :
				$this -> assign("folder_name", '我发布的任务');
				$where['user_id'] = get_user_id();
				break;

			case 'my_assign' :
				$this -> assign("folder_name", '我指派的任务');

				$where_log['assigner'] = get_user_id();
				$task_list = M("TaskLog") -> where($where_log) -> getField('task_id', TRUE);
				if (empty($task_list)) {
					$where['_string'] = '1=2';
				} else {
					$where['id'] = array('in', $task_list);
				}
				break;

			default :
				break;
		}

		$model = D('Task');
		if (!empty($model)) {
			$this -> _list($model, $where);
		}
		$this -> display();
	}

	public function edit($id) {
		$plugin['uploader'] = true;
		$plugin['editor'] = true;
		$plugin['date'] = true;
		$this -> assign("plugin", $plugin);

		$model = M("Task");
		$this -> _edit($id);
	}

	public function del($id) {
		$this -> _del($id);
	}

	public function add() {
		$plugin['uploader'] = true;
		$plugin['editor'] = true;
		$plugin['date'] = true;
		$this -> assign("plugin", $plugin);
		$this -> display();
	}

	public function read($id) {
		$plugin['uploader'] = true;
		$plugin['jquery-ui'] = true;
		$plugin['editor'] = true;
		$plugin['date'] = true;

		$this -> assign("plugin", $plugin);
		$auth = $this -> config['auth'];
		$this -> assign('auth', $auth);

		$this -> assign('task_id', $id);

		$model = M("Task");
		$vo = $model -> find($id);
		$this -> assign('vo', $vo);

		$where_log['task_id'] = array('eq', $id);
		$task_log = M("TaskLog") -> where($where_log) -> select();
		$this -> assign('task_log', $task_log);
		if (empty($vo['executor'])) {
			$this -> assign('no_assign', 1);
		}

		$where_accept['status'] = 0;
		$where_accept['task_id'] = $id;
		$where_accept['type'] = 1;
		$where_accept['executor'] = array('eq', get_user_id());
		$task_accept = M("TaskLog") -> where($where_accept) -> find();

		if ($task_accept) {
			$this -> assign('is_accept', 1);
			$this -> assign('task_log_id', $task_accept['id']);
		}

		if ($this -> config['auth']['admin']) {
			$where_dept_accept['status'] = 0;
			$where_dept_accept['task_id'] = $id;
			$where_dept_accept['type'] = 2;
			$where_dept_accept['executor'] = array('eq', get_dept_id());
			$task_dept_accept = M("TaskLog") -> where($where_dept_accept) -> find();
			if ($task_dept_accept) {
				$this -> assign('is_accept', 1);
				$this -> assign('task_log_id', $task_dept_accept['id']);
			}
		}

		$where_working['status'] = array('in', '0,10');
		$where_working['task_id'] = $id;
		$where_working['type'] = 1;
		$where_working['executor'] = array('eq', get_user_id());
		$task_working = M("TaskLog") -> where($where_working) -> find();

		if (empty($task_working) && $auth['write']) {
			$where_working['type'] = 2;
			$where_working['executor'] = array('eq', get_dept_id());
			$task_working = M("TaskLog") -> where($where_working) -> find();
		}

		if ($task_working) {
			$this -> assign('is_working', 1);
			$this -> assign('task_working', $task_working);
		}
		$this -> display();
	}

	function let_me_do($task_id) {
		if (IS_POST) {
			M("Task") -> where("id=$task_id") -> setField('executor', get_user_name() . "|" . get_user_id());
			M("Task") -> where("id=$task_id") -> setField('status', 10);

			$data['task_id'] = I('task_id');
			$data['executor'] = get_user_id();
			$data['executor_name'] = get_user_name();
			$data['transactor'] = get_user_id();
			$data['transactor_name'] = get_user_name();
			$data['status'] = 10;
			$data['type'] = 1;
						
			$task_id = I(task_id);
			$list = M("TaskLog") -> add($data);
			if ($list != false) {
				//$this -> _add_to_schedule($task_id);
				$return['info'] = '接受成功';
				$return['status'] = 1;
				$this -> ajaxReturn($return);
			} else {
				$this -> error('提交成功');
			}
		}
	}

	public function save_log($id) {
		$model = D("TaskLog");
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}
		$model -> transactor = get_user_id();
		$model -> transactor_name = get_user_name();

		$status = I('status');
		$finish_rate = I('finish_rate');
		$task_log_id = $id;

		if ($finish_rate == '100.00') {
			$model -> finish_time = to_date(time());
			$model -> status = 20;
			$status = 20;
		}
		
		if($model -> status==22){
			$model -> finish_time = to_date(time());
		}

		$list = $model -> save();

		$task_id = M("TaskLog") -> where("id=$task_log_id") -> getField('task_id');

		if ($status == 10) {
			M("Task") -> where("id=$task_id") -> setField('status', 10);
		}

		if ($status >= 20) {
			$where_total_count['task_id'] = array('eq', $task_id);
			$total_count = M("TaskLog") -> where($where_total_count) -> count();

			$where_finish_count['task_id'] = array('eq', $task_id);
			$where_finish_count['status'] = array('egt', 20);
			$finish_count = M("TaskLog") -> where($where_finish_count) -> count();

			if ($total_count == $finish_count) {
				M("Task") -> where("id=$task_id") -> setField('status', 30);

				$user_id = M('Task') -> where("id=$task_id") -> getField('user_id');

				$task = M("Task") -> where("id=$task_id") -> find();

				$transactor_name = get_user_name();

				$push_data['type'] = '任务';
				$push_data['action'] = '已完成';
				$push_data['title'] = "{$transactor_name}已完成您发起的[{$task['name']}]任务";
				$push_data['content'] = "如有问题，请与[{$transactor_name}]进行沟通。";
				$push_data['url'] = U('Task/read',"id={$task['id']}&return_url&Task/index");

				send_push($push_data, $user_id);
			}
		}

		if ($status == 21) {
			$task_id = I('task_id');
			$forword_executor = I('forword_executor');
			D('Task') -> forword($task_id, $forword_executor);
		}
		if ($status == 22) {

		}
		if ($list !== false) {
			$this -> assign('jumpUrl', get_return_url());
			$this -> success('提交成功!');
			//成功提示
		} else {
			$this -> error('提交失败!');
			//错误提示
		}
	}

	function upload() {
		$this -> _upload();
	}

	function down($attach_id) {
		$this -> _down($attach_id);
	}

	private function _add_to_schedule($task_id) {
		$info = M("Task") -> where("id=$task_id") -> find();
		$data['name'] = $info['name'];
		$data['content'] = $info['content'];
		$data['start_time'] = to_date(time());
		$data['end_time'] = $info['expected_time'];
		$data['user_id'] = get_user_id();
		$data['user_name'] = get_user_name();
		$data['priority'] = 3;

		$list = M('Schedule') -> add($data);
	}

	function _send_mail_finish($task_id, $executor) {
		$executor_info = M("User") -> where("id=$executor") -> find();

		$email = $executor_info['email'];
		$user_name = $executor_info['name'];

		$info = M("Task") -> where("id=$task_id") -> find();

		$transactor_name = M("TaskLog") -> where("task_id=$task_id") -> getField('id,transactor_name');

		$transactor_name = implode(",", $transactor_name);

		$title = "您发布任务已完成：" . $info['name'];

		$body = "您好，{$user_name}，{$transactor_name} 完成了您发起的[{$info['name']}]任务</br>";
		$body .= "任务主题：{$info['name']}</br>";
		$body .= "任务时间：{$info['expected_time']}</br>";
		$body .= "任务执行人：{$transactor_name}</br>";
		$body .= "请及时检查任务执行情况，如有问题，请与[{$transactor_name}]进行沟通。</br>";
		$body .= "任务完成后请对[任务执行人]表达我们的感谢。</br>";

		$body .= "点击查看任务详情：http://" . $_SERVER['SERVER_NAME'] . U('Task/read', 'id=' . $info['id']) . "</br>";
		$body .= "霞湖世家，感谢有您！</br>";

		send_mail($email, $user_name, $title, $body);
	}
}
