<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class CommentModel extends CommonModel {
	protected $_validate	 =	 array(
			array('nickname','require','姓名必须填写！'),
			array('title','require','主题必须填写！'),
			array('contents','require','内容必须填写！')
		);

	protected $_auto	 =	 array(
			array('create_time','time',1,'function'),
			array('update_time','time',3,'function')
		);
}
?>