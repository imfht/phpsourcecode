<?php 
/*
 * 后台登陆次数管理模型
 */
namespace Admin\Model;
use Think\Model;
class TimesModel extends Model {
	protected $tableName = 'admin_times';
	
	public function check_username($username){
		//找出此用户名的信息
		$where['username'] = $username;
		$result = $this->where($where)->find();
		return $result ? $result['times'] : 0;
	}
}
