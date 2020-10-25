<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 --------------------------------------------------------------*/

namespace Home\Controller;
use Think\Controller;

class PushController extends HomeController {
	protected $is_close = false;
	protected $config = array('app_type' => 'asst');

	function index() {
		$this -> redirect('folder', array('type' => 'all'));
	}

	function server() {
		$user_id = get_user_id();
		session_write_close();
		$data = $this -> get_data($user_id);
		$start_time = time();

		$response = array();
		if (empty($data)) {
			$response['status'] = 0;
			$response['timestamp'] = $start_time;
		} else {
			$response['status'] = 1;
			$response['data'] = $data;
			$response['timestamp'] = time();
			$response['count']=$this->get_count($user_id);
		}
		echo json_encode($response);
		flush();
		die ;
	}

	function get_data($user_id) {
		$where['user_id'] = array('eq',$user_id);
		$where['time'] = array('elt', time() - 1);
		$model = M("Push");
		$data = $model -> where($where) -> find();
		if ($data) {
			$model -> delete($data['id']);
		}
		return $data;
	}

	function get_count($user_id) {
		$where['user_id'] = array('eq',$user_id);
		$where['time'] = array('elt', time() - 1);
		return M("Push")->where($where)->count();;
	}
}
?>