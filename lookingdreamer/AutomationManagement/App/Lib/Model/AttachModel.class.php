<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

// 附件模型
class AttachModel extends CommonModel {
	protected $_validate = array(
		array('name','require','名称必须'),
		);

	protected $_auto		=	array(
		array('create_time','time','function',self::MODEL_INSERT),
		array('update_time','time','function',self::MODEL_UPDATE),
		array('user_id','getMemberId','callback',self::MODEL_INSERT),
		);
}
?>