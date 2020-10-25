<?php
namespace app\admin\model;

use think\Model;

/**
* 管理员模型类
*/
class Admin extends Model
{
	
	function initialize()
	{
		parent::initialize();
	}

	function do_login($params){
		if(empty($params['username'])) {
            return FALSE;
        }
        if(empty($params['password'])) {
            return FALSE;
        }
        $admin_user = $this->where('username',$params['username'])->find();
        if(!$admin_user) {
            return FALSE;
        }
        $admin_user = $admin_user->toArray();
        if($admin_user['password'] !== strtolower(md5($params['password']))) {
            return FALSE;
        }
        session('admin_user',$admin_user);
        return $admin_user;
	}

	function edit($params){
		$result = $this->isUpdate(true)->allowField(true)->save($params);
		if($result){
			return true;
		}else{
			return false;
		}
	}

}