<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class FocuspicModel extends CommonModel {
	protected $_validate = array(
        array('title','require','标题必须')
		);

	protected $_auto		=	array(
			array('create_time','time',self::MODEL_INSERT,'function'),
			array('update_time','time',self::MODEL_BOTH,'function'),
			array('start_time','strtotime',3,'function'),
			array('end_time','strtotime',3,'function')		
		);
}
?>