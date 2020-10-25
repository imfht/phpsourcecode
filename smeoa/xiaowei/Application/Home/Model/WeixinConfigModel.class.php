<?php
/*---------------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
 -------------------------------------------------------------------------*/

// 节点模型
namespace Home\Model;
use Think\Model;

class  WeixinConfigModel extends CommonModel {
	protected $_validate = array( array('controller', 'check_controller', '已经存在', 0, 'callback'), );

	public function check_controller() {
		$map['controller'] = I('controller');
		$result = $this -> where($map) -> find();
		if ($result) {
			return false;
		} else {
			return true;
		}
	}
}
?>