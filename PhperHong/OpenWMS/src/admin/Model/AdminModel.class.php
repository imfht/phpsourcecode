<?php
// +----------------------------------------------------------------------
// | openWMS (开源wifi营销平台)
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2025 http://cnrouter.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.gnu.org/licenses/gpl-2.0.html )
// +----------------------------------------------------------------------
// | Author: PhperHong <phperhong@cnrouter.com>
// +----------------------------------------------------------------------
namespace admin\Model;
use Think\Model;
use Think\Exception;
class AdminModel extends Model{
	protected $handler ;
	protected $cache;
 	function __construct() {
 		$this->handler = M('admins');
 	}
 	/**
	 +----------------------------------------------------------
	 * 校验用户名和密码是否正确
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $username 用户名
	 * @param $password 密码
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function check_user($username, $password){
 		if (empty($username) || empty($password)){
 			throw new Exception("请填写用户名或密码");
 		}

 		//截取5位字符串加密验证
 		$password = substr($password, 5, 5);

 		$user_info = $this->handler->field('id, username, parent_uid, group_id')->where(array('username'=>$username, 'password'=>md5($password)))->find();
 		if (!is_array($user_info)){
 			throw new Exception('抱歉，用户名或密码错误，请重试!');
 		}
 		$allow_modify = 9999;
 		$merchant_limit = 9999;
 		//如果是代理商，则获取详细信息进行缓存
 		$url = U('Index/index');
 		
 		session('allow_modify', $allow_modify);
		session('merchant_limit', $merchant_limit);
 		session('adminid', $user_info['id']);
 		session('username', $user_info['username']);
 		session('parent_uid', $user_info['parent_uid']);
 		session('group_id', $user_info['group_id']);
 		$user_info['url'] = $url;
 		return $user_info;
 	}
 	/**
	 +----------------------------------------------------------
	 * 检测用户是否存在
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $username 用户名
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function check_username($username){
 		if (empty($username)){
 			throw new Exception("请填写用户名");
 		}

 		

 		$count = $this->handler->where(array('username'=>$username))->count();
 		if ($count>0){
 			throw new Exception('用户名已存在');
 		}
 		return true;
 	}
 	
 	/**
	 +----------------------------------------------------------
	 * 根据组编号获取功能菜单
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $group_id 组编号
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function get_menu_list_by_groupid($group_id = ''){
		
	 	$menu_list = C('MENU_LIST');
	 	return $menu_list[3];
	 	
	}
	/**
	 +----------------------------------------------------------
	 * 检查是否登录
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	*/
	public function check_login(){
		$adminid = session('adminid');
		if (empty($adminid)){
			return false;
		}
		return true;
	}
	/**
	 +----------------------------------------------------------
	 * 清除session，退出登录
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	*/
	public function logout(){
		session(null);
		return true;
	}
	
	/**
	 +----------------------------------------------------------
	 * 修改密码
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	*/
	public function update_password1($passwd_old, $passwd_new1, $passwd_new2){
		if ($passwd_new1 != $passwd_new2){
			throw new Exception("两次新密码不一致，请确认", 1);
		}
		//截取5位字符串加密验证
 		$password = substr($passwd_old, 5, 5);
 		$count = $this->handler->where(array('activated'=>1, 'id'=>session('adminid'), 'password'=>md5($password)))->count();
 		if ($count == 0){
 			throw new Exception('抱歉，原密码错误，请确认!');
 		}
 		$newpassword = substr($passwd_new1, 5, 5);
 		$rs = $this->handler->where(array('id'=>session('adminid')))->save(array(
 			'password'	=> md5($newpassword),
 		));
 		if ($rs === false){
 			throw new Exception("修改密码失败，请重试", 1);
 		}
 		return true;
	}
	
	/**
	 +----------------------------------------------------------
	 * 检查用户是否存在
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	*/
	public function get_admin_by_id($username){
		return $this->handler->where(array('username'=>$username))->find();
	}
	/**
	 +----------------------------------------------------------
	 * 重置密码
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return bool
	 +----------------------------------------------------------
	*/
	public function update_password($id, $password){
		$password = substr($password, 5, 5);
		return $this->handler->where(array('id'=>$id))->save(array('password'=>md5($password)));
	}
}