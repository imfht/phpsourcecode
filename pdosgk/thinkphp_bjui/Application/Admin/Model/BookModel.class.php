<?php 
/*
 * 小说模型
 */
namespace Admin\Model;
use Think\Model;
class BookModel extends Model {
	
	protected $_validate = array(
			array('title','require','标题不能为空！', 1), 
// 			array('content','require','内容不能为空！', 1), 
//  			array('catid','require','栏目不能为空',1, '', 1), 
// 			array('value',array(1,2,3),'值的范围不正确！',2,'in'), // 当值不为空的时候判断是否在一个范围内
// 			array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
// 			array('password','checkPwd','密码格式不正确',0,'function'), // 自定义函数验证密码格式 
	);

}
