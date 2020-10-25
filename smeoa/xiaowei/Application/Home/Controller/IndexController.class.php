<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 --------------------------------------------------------------*/

namespace Home\Controller;

class IndexController extends HomeController {
	protected $config = array('app_type' => 'public');
	//过滤查询字段

	public function index() {
		$plugin['jquery-ui'] = true;
		$this -> assign("plugin", $plugin);

		cookie("current_node", null);
		cookie("top_menu", null);

		$config = D("UserConfig") -> get_config();
		$this -> assign("home_sort", $config['home_sort']);

		$this -> _mail_list();
		$this -> _flow_list();
		$this -> _schedule_list();
		$this -> _info_list();

		//$this -> _udf_sales_list();
		//$this -> _udf_renew_list();

		$this -> display();
	}

	public function _udf_sales_list() {

		$node_model = M("UdfShop");
		$node_list = $node_model -> order('sort asc') -> select();

		$node_list = tree_to_list(list_to_tree($node_list));

		$start_date = date("Y-m-d", mktime(0, 0, 0, date("m"), 1, date("Y")));

		$end_date = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));

		$target_list = D('UdfSales') -> get_target($start_date, $end_date);

		$present_list = D('UdfSales') -> get_sumary($start_date, $end_date);

		foreach ($node_list as $key => $val) {

			$shop_no = rotate(tree_to_list(list_to_tree($node_list, $val['id'])));
			if (!empty($shop_no)) {
				$shop_no = $val['shop_no'] . "," . implode(',', $shop_no['shop_no']);
			} else {
				$shop_no = $val['shop_no'];
			}

			$t = date('t');
			$d = date('d') - 1;
			$target_rate = $d / $t;

			$target = $this -> _get_filter_data($target_list, $shop_no);
			$present_month = $this -> _get_filter_data($present_list, $shop_no);

			$node_list[$key]['A1'] = round($present_month / $target, 4) * 100;
			$node_list[$key]['A2'] = $target_rate - $present_month / $target;
		}

		foreach ($node_list as $key => $val) {
			if ($val['level'] == 0) {
				$new[] = $val;
			}
		}

		$this -> assign('sales_node_list', $new);

		$target_sum = array_sum($target_list);

		$present_sum = array_sum($present_list);

		$sum['A1'] = round($present_sum / $target_sum, 4) * 100;
		$sum['A2'] = $target_rate - $present_sum / $target_sum;

		$this -> assign('sales_sum', $sum);
	}

	public function _udf_renew_list() {

		$node_model = M("UdfShop");
		$node_list = $node_model -> order('sort asc') -> select();

		$node_list = tree_to_list(list_to_tree($node_list));

		$start_date = date("Y-m-d", mktime(0, 0, 0, date("m"), 1, date("Y")));

		$end_date = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));

		$target_list = D('UdfRenew') -> get_target($start_date, $end_date);

		$present_list = D('UdfRenew') -> get_sumary($start_date, $end_date);

		foreach ($node_list as $key => $val) {

			$shop_no = rotate(tree_to_list(list_to_tree($node_list, $val['id'])));
			if (!empty($shop_no)) {
				$shop_no = $val['shop_no'] . "," . implode(',', $shop_no['shop_no']);
			} else {
				$shop_no = $val['shop_no'];
			}

			$t = date('t');
			$d = date('d') - 1;
			$target_rate = $d / $t;

			$target = $this -> _get_filter_data($target_list, $shop_no);
			$present_month = $this -> _get_filter_data($present_list, $shop_no);

			$node_list[$key]['A1'] = round($present_month / $target, 4) * 100;
			$node_list[$key]['A2'] = $target_rate - $present_month / $target;
		}

		foreach ($node_list as $key => $val) {
			if ($val['level'] == 0) {
				$new[] = $val;
			}
		}

		$this -> assign('renew_node_list', $new);

		$target_sum = array_sum($target_list);

		$present_sum = array_sum($present_list);

		$sum['A1'] = round($present_sum / $target_sum, 4) * 100;
		$sum['A2'] = $target_rate - $present_sum / $target_sum;

		$this -> assign('renew_sum', $sum);
	}

	public function set_sort() {
		$val = I('val');
		$data['home_sort'] = $val;
		$model = D("UserConfig") -> set_config($data);
	}

	protected function _mail_list() {
		$user_id = get_user_id();
		$model = D('Mail');

		//获取最新邮件
		$where['user_id'] = $user_id;
		$where['is_del'] = array('eq', '0');
		$where['folder'] = array( array('eq', 1), array('gt', 6), 'or');

		$new_mail_list = $model -> where($where) -> field("id,name,create_time") -> order("create_time desc") -> limit(8) -> select();
		$this -> assign('new_mail_list', $new_mail_list);

		//获取未读邮件
		$where['read'] = array('eq', '0');
		$unread_mail_list = $model -> where($where) -> field("id,name,create_time") -> order("create_time desc") -> limit(8) -> select();
		$this -> assign('unread_mail_list', $unread_mail_list);
	}

	protected function _flow_list() {
		$user_id = get_user_id();
		$emp_no = get_emp_no();
		$model = D('Flow');
		//带审批的列表
		$FlowLog = M("FlowLog");
		$where['emp_no'] = $emp_no;
		$where['is_del'] = 0;
		$where['_string'] = "result is null";
		$log_list = $FlowLog -> where($where) -> field('flow_id') -> select();
		$log_list = rotate($log_list);

		if (!empty($log_list)) {
			$map['id'] = array('in', $log_list['flow_id']);
		} else {
			$map['_string'] = '1=2';
		}
		$todo_flow_list = $model -> where($map) -> field("id,name,create_time") -> limit(8) -> order("create_time desc") -> select();
		$this -> assign("todo_flow_list", $todo_flow_list);

		//已提交
		$map = array();
		$map['user_id'] = $user_id;
		$map['step'] = array('gt', 10);

		$submit_flow_list = $model -> where($map) -> field("id,name,create_time") -> limit(8) -> order("create_time desc") -> select();

		$this -> assign("submit_flow_list", $submit_flow_list);
	}

	protected function _info_list() {
		$user_id = get_user_id();
		$dept_id = get_dept_id();

		$map['_string'] = " Info.is_public=1 or Info.dept_id=$dept_id ";

		$info_list = M("InfoScope") -> where("user_id=$user_id") -> getField('info_id', true);		
			$info_list = implode(",", $info_list);

			if (!empty($info_list)) {
				$map['_string'] .= "or Info.id in ($info_list)";
			}

			$folder_list = D("SystemFolder") -> get_authed_folder("Info");
			
			if ($folder_list) {
				$map['folder'] = array("in", $folder_list);
			} else {
				$map['_string'] = '1=2';
			}
			$map['is_del'] = array('eq', 0);

			$model = D("InfoView");
			
			$info_list = $model -> where($map) -> field("id,name,create_time,folder_name") -> order("create_time desc") -> limit(8) -> select();
		$this -> assign("info_list", $info_list);
	}

	protected function _schedule_list() {
		$user_id = get_user_id();
		$model = M('Schedule');
		//获取最新邮件
		$start_date = date("Y-m-d");
		$where['user_id'] = $user_id;
		$where['is_del'] = array('eq', 0);
		$where['start_time'] = array('egt', $start_date);
		$schedule_list = M("Schedule") -> where($where) -> order('start_time,priority desc') -> limit(8) -> select();
		$this -> assign("schedule_list", $schedule_list);

		$model = M("Todo");
		$where = array();
		$where['user_id'] = $user_id;
		$where['status'] = array("in", "1,2");
		$todo_list = M("Todo") -> where($where) -> order('priority desc,sort asc') -> limit(8) -> select();
		$this -> assign("todo_list", $todo_list);
	}

	private function _get_filter_data($data, $shop_no) {
		$shop_no = array_filter(explode(",", $shop_no));
		$res = 0;

		foreach ($data as $key => $val) {
			if (in_array($key, $shop_no)) {
				$res += $val;
			}
		}
		return $res;
	}

}
?>