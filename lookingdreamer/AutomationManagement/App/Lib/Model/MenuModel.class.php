<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class MenuModel extends CommonModel {
	protected $_validate	=	array(
		array('title','require','名称必须'),
		);
}
?>