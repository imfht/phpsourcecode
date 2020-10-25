<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class ProductModel extends CommonModel {
	protected $_validate	 =	 array(
		array('title','require','产品名称必须！')
	);

	protected $_auto	 =	 array(
		array('create_time','create_time',self::MODEL_BOTH,'callback'),
		array('update_time','time',3,'function')
	);
}
?>