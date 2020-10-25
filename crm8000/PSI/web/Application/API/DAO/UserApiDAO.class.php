<?php

namespace API\DAO;

use Home\DAO\PSIBaseExDAO;

/**
 * 用户 API DAO
 *
 * @author 李静波
 */
class UserApiDAO extends PSIBaseExDAO {

	public function doLogin($params) {
		$loginName = $params["loginName"];
		$password = $params["password"];
		
		$db = $this->db;
		
		$sql = "select id from t_user where login_name = '%s' and password = '%s' and enabled = 1";
		
		$data = $db->query($sql, $loginName, md5($password));
		
		$result = [];
		
		if ($data) {
			return $data[0]["id"];
		} else {
			return null;
		}
	}

	/**
	 * 判断当前用户是否有某个功能的权限
	 *
	 * @param string $userId
	 *        	用户id
	 * @param string $fid
	 *        	功能id
	 * @return boolean true:有该功能的权限
	 */
	public function hasPermission($userId, $fid) {
		$db = $this->db;
		$sql = "select count(*) as cnt
				from  t_role_user ru, t_role_permission rp, t_permission p
				where ru.user_id = '%s' and ru.role_id = rp.role_id
				      and rp.permission_id = p.id and p.fid = '%s' ";
		$data = $db->query($sql, $userId, $fid);
		
		return $data[0]["cnt"] > 0;
	}
}