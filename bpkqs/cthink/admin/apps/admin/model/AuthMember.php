<?php
namespace app\admin\model;
use think\Model;

/**
 * 用户表
 * @author zhanghd <zhanghd1987@foxmail.com>
 */
class AuthMember extends Model
{
	/**
	 * 库名和表名一起配置(方便之后分库处理)
	 */
	protected $table = 'cthink.__AUTH_MEMBER__';
	
	/**
	 * 返回列表结果
	 * @param array $map 搜索条件
	 */
	public function lists($map){
		$list = \think\Db::table($this->table)->where($map)->paginate(15);
		return $list;
	}
	
	/**
	 * 通过id获取一条管理员信息
	 * @param int $id 管理员id
	 */
	public function getFindOne($uid,$field = '*'){
		$admin = \think\Db::table($this->table)->field($field)->where(['uid'=>$uid])->find();
		if(isset($admin['password'])){
			unset($admin['password']);
		}
		return $admin;
	}
	
	/**
	 * 添加管理员信息
	 * @param array $input 要添加的值
	 * @return int pk or false 成功和失败的状态，成功返回当前插入的id，失败返回false
	 */
	public function addMember($input){
		unset($input['password2']);
		$time = time();
		$input['ctime'] = $time;
		$input['utime'] = $time;
		$input['password'] = cthink_md5($input['password']);
		return \think\Db::table($this->table)->insertGetId($input);
		
	}
	
	/**
	 * 编辑管理员信息
	 * @param array $input 要修改的值
	 * @param array $where 修改条件
	 * @return bool true or false 成功和失败的状态
	 */
	public function editMember($input){
		unset($input['password2']);
		if(empty($input['password'])){
			unset($input['password']);
		}else{
			$input['password'] = cthink_md5($input['password']);
		}
		return \think\Db::table($this->table)->update($input);
	}
	
	/**
	 * 设置管理员的状态信息
	 */
	public function stateMember($map,$data){
		return \think\Db::table($this->table)->where($map)->update($data);
	}
	
	/**
	 * 删除管理员
	 * @param string $uidlist 用户逗号(,)隔开的uid，例如：1,4,7
	 */
	public function removeMember($uidlist){
		$map = explode(',',$uidlist);
		return \think\Db::table($this->table)->delete($map);
	}
	
	/**
	 * 验证管理员登录操作
	 */
	public function login($username,$password){
		$map = [
			'username'	=> $username,
			'password'	=> cthink_md5($password),
		];
		$admin_info = \think\Db::table($this->table)->where($map)->field('uid,username,nickname,phone,email,status,is_remove,login_count,last_login_time,last_login_ip')->find();
		return $admin_info;
	}
	
	/**
	 * 验证登录成功之后，将登录信息更新，并且生成session
	 */
	public function updateLoginInfo($admin_info){
		$return  = false;
		$update = [
			'login_count'		=> intval($admin_info['login_count']) + 1,
			'last_login_time'	=> time(),
			'last_login_ip'		=> get_client_ip(),
		];
		$is_update = \think\Db::table($this->table)->where(['uid'=>$admin_info['uid']])->update($update);
		if($is_update){
			session('user_auth',$admin_info);
			$return = true;
		}
		return $return;
	}
}
