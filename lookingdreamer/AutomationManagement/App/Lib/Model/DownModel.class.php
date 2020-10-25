<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class DownModel extends CommonModel {
	protected $_validate = array(
        array('title','require','标题必须'),
        array('content','require','内容必须'),
	);

	protected $_auto		=	array(
		array('create_time','create_time',self::MODEL_BOTH,'callback'),
		array('update_time','time',self::MODEL_BOTH,'function'),
	);
}
?>