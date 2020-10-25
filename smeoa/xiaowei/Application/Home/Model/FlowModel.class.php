<?php
/*---------------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 -------------------------------------------------------------------------*/

namespace Home\Model;
use Think\Model;

class  FlowModel extends CommonModel {
	// 自动验证设置
	protected $_validate = array( array('name', 'require', '标题必须', 1), array('content', 'require', '内容必须'), );

	function _before_insert(&$data, $options) {
		$type = $data["type"];
		$dept_id = get_dept_id();
		$data['dept_id'] = $dept_id;
		$data['dept_name'] = get_dept_name();
		$data['emp_no'] = get_emp_no();

		$doc_no_format = M("FlowType") -> where("id=$type") -> getField("doc_no_format");
		$short_dept = M("Dept") -> where("id=$dept_id") -> getField('short');
		$short_flow = M("FlowType") -> where("id=$type") -> getField('short');

		$sql = "SELECT count(*) count FROM `" . $this -> tablePrefix . "flow` WHERE type=$type ";
		$sql .= " and year(FROM_UNIXTIME(create_time))>=year(now())";

		if (strpos($doc_no_format, "{DEPT}") !== false) {
			$sql .= " and dept_id=" . get_dept_id();
		}
		$rs = $this -> db -> query($sql);
		$count = $rs[0]['count'] + 1;

		if (strpos($doc_no_format, "{DEPT}") !== false) {
			$doc_no_format = str_replace("{DEPT}", $short_dept, $doc_no_format);
		}

		if (strpos($doc_no_format, "{SHORT}") !== false) {
			$doc_no_format = str_replace("{SHORT}", $short_flow, $doc_no_format);
		}

		if (strpos($doc_no_format, "{YYYY}") !== false) {
			$doc_no_format = str_replace("{YYYY}", date('Y', mktime()), $doc_no_format);
		}

		if (strpos($doc_no_format, "{YY}") !== false) {
			$doc_no_format = str_replace("{YY}", date('y', mktime()), $doc_no_format);
		}

		if (strpos($doc_no_format, "{M}") !== false) {
			$doc_no_format = str_replace("{M}", date('m', mktime()), $doc_no_format);
		}
		if (strpos($doc_no_format, "{D}") !== false) {
			$doc_no_format = str_replace("{D}", date('d', mktime()), $doc_no_format);
		}
		if (strpos($doc_no_format, "{#}") !== false) {
			$doc_no_format = str_replace("{#}", str_pad($count, 1, "0", STR_PAD_LEFT), $doc_no_format);
		}
		if (strpos($doc_no_format, "{##}") !== false) {
			$doc_no_format = str_replace("{##}", str_pad($count, 2, "0", STR_PAD_LEFT), $doc_no_format);
		}
		if (strpos($doc_no_format, "{###}") !== false) {
			$doc_no_format = str_replace("{###}", str_pad($count, 3, "0", STR_PAD_LEFT), $doc_no_format);
		}
		if (strpos($doc_no_format, "{####}") !== false) {
			$doc_no_format = str_replace("{####}", str_pad($count, 4, "0", STR_PAD_LEFT), $doc_no_format);
		}
		if (strpos($doc_no_format, "{#####}") !== false) {
			$doc_no_format = str_replace("{#####}", str_pad($count, 5, "0", STR_PAD_LEFT), $doc_no_format);
		}
		if (strpos($doc_no_format, "{######}") !== false) {
			$doc_no_format = str_replace("{######}", str_pad($count, 6, "0", STR_PAD_LEFT), $doc_no_format);
		}
		$data['doc_no'] = $doc_no_format;
	}

	function _after_insert($data, $options) {
		$id = $data['id'];
		if ($data['step'] == 20) {

			$model = M("Flow");

			$where['id'] = array('eq', $id);
			$str_confirm = $this -> _conv_auditor($data['confirm']);
			$str_consult = $this -> _conv_auditor($data['consult']);
			$str_refer = $this -> _conv_auditor($data['refer']);

			$model -> where($where) -> setField('confirm', $str_confirm);
			$model -> where($where) -> setField('consult', $str_consult);
			$model -> where($where) -> setField('refer', $str_refer);

			$this -> next_step($data['id'], 20);
		}
	}

	function _before_update(&$data, $options) {
		$flow_type = M("FlowType") -> find($data['type']);
		if (($flow_type['is_lock'])&&($data['step']>20)) {
			unset($data['confirm']);
			unset($data['consult']);
			unset($data['refer']);
		}
	}

	function _after_update($data, $options) {
		$id = $data['id'];
		$step = $data['step'];

		if ($data['step'] == 20) {
			$model = M("Flow");
			$where['id'] = array('eq', $id);

			$str_confirm = $this -> _conv_auditor($data['confirm']);
			if (!empty($str_confirm)) {
				$model -> where($where) -> setField('confirm', $str_confirm);
			}

			$str_consult = $this -> _conv_auditor($data['consult']);
			if (!empty($str_consult)) {
				$model -> where($where) -> setField('consult', $str_consult);
			}

			$str_refer = $this -> _conv_auditor($data['refer']);
			if (!empty($str_refer)) {
				$model -> where($where) -> setField('refer', $str_refer);
			}

			$this -> next_step($data['id'], $step);
		}
	}

	function _get_dept($dept_id, $dept_grade) {
		$model = M("Dept");
		$dept = $model -> find($dept_id);
		if ($dept['dept_grade_id'] == $dept_grade) {
			return $dept_id;
		} else {
			if ($dept['pid'] != 0) {
				return $this -> _get_dept($dept['pid'], $dept_grade);
			}
		}
		return false;
	}

	function _conv_auditor($val) {
		$arr_auditor = array_filter(explode("|", $val));
		$str_auditor = '';

		foreach ($arr_auditor as $auditor) {
			if (strpos($auditor, "dgp") !== false) {
				$temp = explode("_", $auditor);
				$dept_grade = $temp[1];
				$position = $temp[2];
				$dept_id = $this -> _get_dept(get_dept_id(), $dept_grade);

				$model = M("User");
				$where = array();
				$where['dept_id'] = $dept_id;
				$where['position_id'] = $position;
				$where['is_del'] = 0;
				$emp_list = $model -> where($where) -> select();
				$emp_list = rotate($emp_list);

				if (!empty($emp_list)) {
					$str_auditor .= implode(",", $emp_list['emp_no']) . "|";
				}
			}

			if (strpos($auditor, "dp") !== false) {
				$temp = explode("_", $auditor);
				$dept = $temp[1];
				$position = $temp[2];

				$model = M("User");
				$where = array();
				$where['dept_id'] = $dept;
				$where['position_id'] = $position;
				$where['is_del'] = 0;
				$emp_list = $model -> where($where) -> select();

				$emp_list = rotate($emp_list);

				if (!empty($emp_list)) {
					$str_auditor .= implode(",", $emp_list['emp_no']) . "|";
				}
			}

			if (strpos($auditor, "dept") !== false) {
				$temp = explode("_", $auditor);
				$dept = $temp[1];

				$model = M("User");
				$where = array();
				$where['dept_id'] = $dept;
				$where['is_del'] = 0;
				$emp_list = $model -> where($where) -> select();
				$emp_list = rotate($emp_list);
				if (!empty($emp_list)) {
					$str_auditor .= implode(",", $emp_list['emp_no']) . "|";
				}
			}

			if (strpos($auditor, "emp") !== false) {
				$temp = explode("_", $auditor);
				$emp = $temp[1];
				$str_auditor .= $emp . "|";
			}

			if (strpos($auditor, "_") == false) {
				$str_auditor .= $auditor . "|";
			}
		}
		return $str_auditor;
	}

	public function back_to($flow_id, $emp_no) {

		$model = D("FlowLog");
		$where['flow_id'] = $flow_id;
		$where['emp_no'] = $emp_no;
		$data['step'] = D("FlowLog") -> where($where) -> getField('step');
		if (empty($data['step'])) {
			$data['step'] = 20;
		}

		$data['flow_id'] = $flow_id;
		$data['emp_no'] = $emp_no;

		$model -> create($data);
		$model -> add();

		$flow = M("Flow") -> find($flow_id);

		$push_data['type'] = '审批';
		$push_data['action'] = '被退回';
		$push_data['title'] = $flow['name'];
		$push_data['content'] = '审核人：' . get_dept_name() . "-" . get_user_name();
		$push_data['url'] = U('Flow/read',"id={$flow_id}&return_url=Flow/index");

		$user_id = M("User") -> where(array('emp_no' => $emp_no)) -> getField("id");
		send_push($push_data, $user_id);
	}

	public function next_step($flow_id, $step) {
		$confirm = M("Flow") -> where(array('id' => $flow_id)) -> getField("confirm");
		
		if (!empty($confirm)&&($step==20)){
			$confirm_list = array_filter(explode("|", $confirm));
			$is_include_presenter = array_search(get_emp_no(), $confirm_list);
			if ($is_include_presenter !== false) {
				$step = $step + $is_include_presenter + 1;
			}
		}
		
		$model = D("Flow");
		if (substr($step, 0, 1) == 2) {
			if ($this -> is_last_confirm($flow_id)) {
				$model -> where(array('id' => $flow_id)) -> setField('step', 30);
				$step = 30;
			} else {
				$step++;
			}
		}

		if (substr($step, 0, 1) == 3) {
			if ($this -> is_last_consult($flow_id)) {
				$step = 40;
			} else {
				$step++;
			}
		}

		if ($step == 40) {
			$model -> where(array('id' => $flow_id)) -> setField('step', 40);

			$flow = M("Flow") -> find($flow_id);
			$push_data['type'] = '审批';
			$push_data['action'] = '审核通过';
			$push_data['title'] = $flow['name'];
			$push_data['content'] = '审核人：' . get_dept_name() . "-" . get_user_name();
			$push_data['url'] = U('Flow/read',"id={$flow_id}&return_url=Flow/index");

			send_push($push_data, $flow['user_id']);

		} else {
			$data['flow_id'] = $flow_id;
			$data['step'] = $step;
			$data['emp_no'] = $this -> duty_emp_no($flow_id, $step);

			if (strpos($data['emp_no'], ",") !== false) {
				$emp_list = explode(",", $data['emp_no']);
				foreach ($emp_list as $emp) {
					$data['emp_no'] = $emp;
					$model = D("FlowLog");
					$model -> create($data);
					$model -> add();
				}
			} else {
				$model = D("FlowLog");
				$model -> create($data);
				$model -> add();
			}
		}
	}

	function is_last_confirm($flow_id) {
		$confirm = M("Flow") -> where(array('id' => $flow_id)) -> getField("confirm");
		if (empty($confirm)) {
			return true;
		}
		$confirm_list = array_filter(explode("|", $confirm));
		$last_confirm_emp_no = end($confirm_list);
		if (strpos($last_confirm_emp_no, get_emp_no()) !== false) {
			return true;
		}
		return false;
	}

	function is_last_consult($flow_id) {
		$consult = M("Flow") -> where(array('id' => $flow_id)) -> getField("consult");
		if (empty($consult)) {
			return true;
		}

		$consult_list = array_filter(explode("|", $consult));
		$last_consult_emp_no = end($consult_list);
			
		if (strpos($last_consult_emp_no, get_emp_no()) !== false) {
			return true;
		}
		return false;
	}

	function duty_emp_no($flow_id, $step) {
		if (substr($step, 0, 1) == 2) {
			$confirm = M("Flow") -> where(array('id' => $flow_id)) -> getField("confirm");
			$arr_confirm = array_filter(explode("|", $confirm));

			return $arr_confirm[fmod($step, 10) - 1];
		}

		if (substr($step, 0, 1) == 3) {
			$consult = M("Flow") -> where(array('id' => $flow_id)) -> getField("consult");
			$arr_consult = array_filter(explode("|", $consult));
			return $arr_consult[fmod($step, 10) - 1];
		}
	}

	function send_to_refer($flow_id, $emp_list) {

		$data['flow_id'] = $flow_id;
		$data['result'] = 1;
		$data['step'] = 100;
		$data['create_time'] = time();
		$data['from'] = get_user_name();

		foreach ($emp_list as $key => $val) {
			$data['emp_no'] = $val;
			$where_flow_log['flow_id'] = array('eq', $flow_id);
			$where_flow_log['emp_no'] = array('eq', $val);

			$flow_log = M("FlowLog") -> where($where_flow_log) -> select();
			if (!$flow_log) {
				D("FlowLog") -> add($data);
			} else {
				unset($emp_list[$key]);
			}
		}

		$flow = M("Flow") -> find($flow_id);
		$push_data['type'] = '审批';
		$push_data['action'] = '需要您参阅';
		$push_data['title'] = $flow['name'];
		$push_data['content'] = '转发人：' . get_dept_name() . "-" . get_user_name();
		$push_data['url'] = U('Flow/read',"id={$flow_id}&return_url=Flow/index");

		$where_user_list['emp_no'] = array('in', $emp_list);
		$user_list = M("User") -> where($where_user_list) -> getField("id", true);
		send_push($push_data, $user_list);
	}

}
?>