<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class SeokeyModel extends CommonModel {
	protected $_validate	 =	 array(
		array('title','require','关键词名称必填！')
		);

	protected $_auto	 =	 array(
		array('create_time','time','function',self::MODEL_INSERT),
		array('update_time','time','function',self::MODEL_BOTH),
		);
}
?>