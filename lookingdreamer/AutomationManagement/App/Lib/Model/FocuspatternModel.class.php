<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class FocuspatternModel extends CommonModel {
	protected $_validate = array(
        array('title','require','标题必须'),
        array('focuscode','require','调用代码必须'),
		);

	protected $_auto		=	array(
		array('create_time','time',self::MODEL_INSERT,'function'),
		array('update_time','time',self::MODEL_BOTH,'function'),
		);
}
?>