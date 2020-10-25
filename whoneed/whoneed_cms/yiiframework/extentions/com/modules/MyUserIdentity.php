<?php
/**
 * 用户验证类
 *
 * 用于后台，验证会员的登录
 *
 * @author		黑冰(001.black.ice@gmail.com)
 * @copyright	(c) 2012
 * @version		$Id$
 * @package		com.modules
 * @since		v0.1
 */
	// 用户验证类
	class MyUserIdentity extends CUserIdentity
	{

		// 管理员登陆
		public function admin_authenticate()
		{
			$CDbCriteria = new CDbCriteria;
			$CDbCriteria->condition = "user_name = :user_name";
			$CDbCriteria->params    = array(':user_name'    => $this->username);
			$row = Whoneed_admin::model()->find($CDbCriteria);
			if(!$row || $row->user_pass !== MyFunction::funHashPassword($this->password, true)){
				return FALSE;
			}else{
                $this->setState('admin_id',		$row->id);
                $this->setState('user_name',	$row->user_name);
                $this->setState('role_id',		$row->role_id);
                $this->setState('admin_is_login', TRUE);
                if( preg_match('/^[\d]{0,6}$/',$this->password) ){
                    $this->setState('weak_password', true);
                }else{
                    $this->setState('weak_password', false);
                }
                return $row->id;
			}
		}

		/**
		 * 登陆接口的用户验证
		 *
		 */
		public function authenticate()
		{
			$CDbCriteria = new CDbCriteria;
			$CDbCriteria->condition = "username = :username";
			$CDbCriteria->params = array(':username' => $this->username);
			$row = Vchat_user::model()->find($CDbCriteria);
			if(!$row || $row->password !== MyController::hash_password($this->password)) {
				return FALSE;
			}
			else
			{
				//更新登陆后的信息
				$row->login_time = date('Y-m-d H:i:s', time());
				$row->login_ip = MyController::getUserHostAddress();
				$row->login_count = $row->login_count + 1;
				$row->save();
				$this->setState('user_is_login', TRUE);
				$this->setState('uid', $row->id);
				$this->setState('username', $row->username);
				$this->setState('nickname',$row->nickname);
				$this->username = $row->username;
				return $row->id;
			}
		}

		//第三方接口的登录认证
		public function third_part_authenticate($row)
		{
		    //更新登陆后的信息
		    $row->login_time = date('Y-m-d H:i:s', time());
		    $row->login_ip = MyController::getUserHostAddress();
		    $row->login_count = $row->login_count + 1;
		    $row->save();
		    $this->setState('user_is_login', TRUE);
		    $this->setState('uid', $row->id);
		    $this->setState('username', $row->username);
		    $this->setState('nickname',$row->nickname);
		    $this->username = $row->username;
		    return $row->id;
		}
	}

?>