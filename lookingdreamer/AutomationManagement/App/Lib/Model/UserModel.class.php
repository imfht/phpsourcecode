<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

// 管理用户模型
class UserModel extends CommonModel {
	protected $_validate	=	array(
			array('account','require','帐号必须填写'),
			array('password','require','密码必须')
		);

	protected $_auto		=	array(
		array('password','pwdHash','callback',self::MODEL_BOTH),
		array('create_time','time','function',self::MODEL_INSERT),
		array('update_time','time','function',self::MODEL_UPDATE),
		);

		protected function pwdHash() {
			if(isset($_POST['password'])) {
				return pwdHash($_POST['password']);
			}else{
				return false;
			}
		}
}
?>