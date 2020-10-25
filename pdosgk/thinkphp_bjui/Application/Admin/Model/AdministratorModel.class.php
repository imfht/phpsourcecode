<?php 
/*
 * 后台管理员模型
 */
namespace Admin\Model;
use Think\Model;
class AdministratorModel extends Model {
	
	/*
	 * 登录验证部分
	 * @garam 验证码/密码
	 * @return
	 *  1用户名错误 
	 *  2账号禁用
	 *  3密码错误
	 *  
	 */
	function Login($username, $Password) {
		//检测用户名
		$Detail = $this->where('username=\'' . $username . '\'')->find();
		if (! $Detail)
			return -1;
		elseif ($Detail['enable'] == 0){
			return -2;
		}
		elseif ($Detail ['password'] == md5 ( $Password )){
			//更新登录时间
			//$DB->UpdateWhere('db_user', array('last_login_time'=> time()), 'uid='. $UserDetail['uid']);
			//$this->where('id=' . $Detail['id'])->save(array('last_login_time'=> time()));
			return $Detail;
		} else
			return -3;
	}
}
