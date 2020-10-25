<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class TplstyleModel extends CommonModel {
	protected $_validate	 =	 array(
			array('title','require','样式名称必须！'),
			array('styledir','checkStyledir','样式目录不存在！',0,'function'),
		);

	protected $_auto	 =	 array(
			array('create_time','time',1,'function'),
			array('update_time','time',3,'function')
		);
}
?>