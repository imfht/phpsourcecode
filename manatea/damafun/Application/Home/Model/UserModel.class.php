<?php 
namespace Home\Model;
use Think\Model;
class UserModel extends Model{
	protected $_validate=array(
		array('name','5,12','用户名的长度必须在5-16之间！',0,'length',3),
		array('repassword','password','确认密码不正确',0,'confirm')
	);
}
?>