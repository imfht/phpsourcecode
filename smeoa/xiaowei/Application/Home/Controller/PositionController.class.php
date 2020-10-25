<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
--------------------------------------------------------------*/

namespace Home\Controller;

class PositionController extends HomeController {
	protected $config = array('app_type' => 'master');

	function _search_filter(&$map) {
		$keyword = I('keyword');
		if (!empty($keyword)) {
			$map['position_no|name'] = array('like', "%" . $keyword . "%");
		}
	}

	function del() {
		$id = $_POST['id'];
		$this -> _destory($id);
	}

}
?>