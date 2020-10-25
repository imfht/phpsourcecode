<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
/**
 * 自定义CI
 *
 * @category Models
 * @author 二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class Auth {
	
	// CI
	var $CI;
	
    // 默认session保存时间60分钟 单位是秒
	var $session_expire = 3600;
	
	/**
	 * 构造函数
	 */
	function __construct() {
		$this->CI = &get_instance ();
		$this->CI->load->database ();
		$this->CI->load->library ( 'encrypt' );
		$this->CI->load->helper ( 'date' );
		
		// session信息
		$manager_session_config = array (
				'sess_cookie_name' => 'manager_session_config',
				'sess_expiration' => 0 
		);
		$this->CI->load->library ( 'session', $manager_session_config, 'manager_session' );
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 判断是否已经登录
	 */
	function is_logged_in($redirect = false, $default_redirect = true) {
		
		// 得到manager_session中用户信息
		$manager = $this->CI->manager_session->userdata ( 'manager' );
		if (! $manager) { // 无用户信息跳到登录页面
			if ($redirect) {
				$this->CI->manager_session->set_userdata ( 'redirect', $redirect );
			}
			if ($default_redirect) {
				redirect ( $this->CI->config->item ( 'admin_folder' ) . 'login?redirect=' . $redirect );
			}
			return false;
		} else { // 有用户信息
			if ($manager ['expire'] && $manager ['expire'] < time ()) { // session已过期
				$this->CI->manager_session->unset_userdata ( 'manager' );
                $this->CI->lang->load ( 'admin_common' );
                //登录超时
                $this->CI->manager_session->set_flashdata ( 'message', lang('timeout_logout') );
				if ($redirect) {
                    $this->CI->manager_session->set_userdata ( 'redirect', $redirect );
				}
				if ($default_redirect) {
					redirect ( $this->CI->config->item ( 'admin_folder' ) . 'login?redirect=' . $redirect );
				}
				return false;
			} else { // session未过期
				if ($manager ['expire']) {
					$manager ['expire'] = time () + $this->session_expire;
					$this->CI->manager_session->set_userdata ( array (
							'manager' => $manager 
					) );
				}
//                else{
//                    $manager_result = $this->get_one_manager ( $this->CI->manager_session->userdata ( 'manager' ) );
//                    if($manager_result){
//                        // 记录管理员登录信息
//                        $this->save_one_manager_logging ( $manager_result );
//                        // 更新管理员登录时间
//                        $this->update_one_manager ( $manager_result );
//                    }
//
//                }
				return true;
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 管理员登录
	 */
	function login_manager($username, $password, $remember = false) {
		$this->CI->load->helper ( 'my_md5' );
		$this->CI->db->select ( '*' );
		$this->CI->db->where ( 'username', $username );
		$this->CI->db->where ( 'password', str_md5 ( $password ) );
		$this->CI->db->limit ( 1 );
		$result = $this->CI->db->get ( 'manager' );
		$result = $result->row_array ();
		
		if (sizeof ( $result ) > 0) {
			$manager = array ();
			$manager ['manager'] = array ();
			$manager ['manager'] ['id'] = $result ['id'];
			$manager ['manager'] ['username'] = $result ['username'];
			$manager ['manager'] ['role_id'] = $result ['role_id'];
			$manager ['manager'] ['status'] = $result ['status'];
			if (! $remember) { // 未记住密码
				$manager ['manager'] ['expire'] = time () + $this->session_expire;
			} else { // 记住密码
				$manager ['manager'] ['expire'] = false;
			}
			$this->CI->manager_session->set_userdata ( $manager );
			return true;
		} else {
			return false;
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 获取菜单数据
	 *
	 * @access public
	 * @param
	 *        	id 管理员id
	 * @return array 菜单数据
	 */
	function get_menu($id = 0) {
		
		// 得到管理员角色
		$role_data = $this->get_one_role_by_id ( $id );
		// 管理员拥有的权限
		$role_power_ids = $role_data ? explode ( ',', $role_data->powers ) : array ();
		if (! $role_power_ids) {
			return array ();
		}
		// 获取权限
		$power = $this->CI->db->select ( '*' )->from ( $this->CI->db->dbprefix ( 'power' ) )->where ( array (
				'pid' => 0,
				'status' => 1 
		) )->where_in ( 'id', $role_power_ids )->order_by ( 'rank', 'asc' )->get ()->result_array ();
		
		foreach ( $power as $p_num => $p_data ) {
			$pd_sub = $this->CI->db->select ( '*' )->from ( $this->CI->db->dbprefix ( 'power' ) )->where ( array (
					'pid' => $p_data ['id'],
					'status' => 1 
			) )->where_in ( 'id', $role_power_ids )->order_by ( 'rank', 'asc' )->get ()->result_array ();
			foreach ( $pd_sub as $ps_num => $ps_data ) {
				$pd_sub [$ps_num] ['sub_power'] = $this->CI->db->select ( '*' )->from ( $this->CI->db->dbprefix ( 'power' ) )->where ( array (
						'pid' => $ps_data ['id'],
						'status' => 1 
				) )->where_in ( 'id', $role_power_ids )->order_by ( 'rank', 'asc' )->get ()->result_array ();
			}
			$power [$p_num] ['sub_power'] = $pd_sub;
		}
		return $power;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 检查是否具有访问权限
	 *
	 * @access public
	 * @param
	 *        	string 权限名称
	 * @return boolean 是否
	 */
	function check_power($power_name, $manager_id) {
		// 权限id
		$power_data = $this->CI->db->select ( 'id' )->from ( $this->CI->db->dbprefix ( 'power' ) )->where ( array (
				'name' => $power_name,
				'status' => 1 
		) )->get ()->row ();
		// 管理员角色
		$role_data = $this->get_one_role_by_id ( $manager_id );
		// 管理员所拥有权限
		$role_power_ids = $role_data ? explode ( ',', $role_data->powers ) : array ();
		if ($power_data && in_array ( $power_data->id, $role_power_ids )) {
			return TRUE;
		}
		return FALSE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 得到管理员信息 manager return manager
	 */
	function get_one_manager($manager) {
		$this->CI->db->select ( 'manager.*,role.name as rolename' );
		$this->CI->db->from ( 'manager' );
		$this->CI->db->join ( 'role', 'role.id=manager.role_id', 'left' );
		$this->CI->db->where ( 'manager.id', $manager ['id'] );
		$result = $this->CI->db->get ();
		$result = $result->row ();
		if (sizeof ( $result ) > 0) {
			return $result;
		} else {
			return false;
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 通过管理员角色 id 得到角色信息 manager return role
	 */
	function get_one_role($manager) {
		$this->CI->db->select ( '*' );
		if (is_array ( $manager )) {
			$this->CI->db->where ( 'id', $manager ['role_id'] );
		} else {
			$this->CI->db->where ( 'id', $manager->role_id );
		}
		$result = $this->CI->db->get ( 'role' );
		$result = $result->row ();
		if (sizeof ( $result ) > 0) {
			return $result;
		} else {
			return false;
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 通过id查询角色 id return role
	 */
	function get_one_role_by_id($id = 0) {
		$this->CI->db->select ( ' * ' );
		$this->CI->db->where ( 'id', $id );
		$result = $this->CI->db->get ( 'role' );
		$result = $result->row ();
		if (sizeof ( $result ) > 0) {
			return $result;
		} else {
			return false;
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 保存管理登录信息 manager
	 */
	function save_one_manager_logging($manager) {
		$this->CI->lang->load ( 'admin_common' );
        $this->CI->load->helper('url');

		$role = $this->get_one_role ( $manager );

		if (is_array ( $manager )) {
			$this->CI->db->insert ( 'manager_logging', array (
					'username' => $manager ['username'],
					'activity' => lang ( 'loggin_manager_role' ) . $role->name . lang ( 'loggin_manager_username' ) . $manager ['username'] . lang ( 'loggin_manager_symbol' ) . lang ( 'loggin_manager_is_log' ),
					'url' => uri_string(),
					'role_id' => $manager ['role_id'],
					'time' => now (),
					'ip' => $this->CI->input->ip_address () 
			) );
		} else {
			$this->CI->db->insert ( 'manager_logging', array (
					'username' => $manager->username,
                    'activity' => lang ( 'loggin_manager_role' ) . $role->name . lang ( 'loggin_manager_username' ) . $manager->username . lang ( 'loggin_manager_symbol' ) . lang ( 'loggin_manager_is_log' ),
					'url' => uri_string(),
					'role_id' => $manager->role_id,
					'time' => now (),
					'ip' => $this->CI->input->ip_address () 
			) );
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 更新管理员登录时间
	 */
	function update_one_manager($manager) {
		$this->CI->db->where ( 'id', $manager->id );
		$this->CI->db->update ( 'manager', array (
				'last_log_time' => $manager->now_log_time,
				'now_log_time' => now () 
		) );
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 退出
	 */
	function logout() {
		$this->CI->manager_session->unset_userdata ( 'manager' );
		$this->CI->manager_session->unset_userdata ( 'redirect' );
		$this->CI->manager_session->sess_destroy ();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * This function takes admin array and inserts/updates it to the database
	 */
	function save($admin) {
		if ($admin ['id']) {
			$this->CI->db->where ( 'id', $admin ['id'] );
			$this->CI->db->update ( 'admin', $admin );
		} else {
			$this->CI->db->insert ( 'admin', $admin );
		}
	}

	/**
	 * This function resets the admins password and emails them a copy
	 */
	function reset_password($email) {
		$admin = $this->get_admin_by_email ( $email );
		if ($admin) {
			$this->CI->load->helper ( 'string' );
			$this->CI->load->library ( 'email' );

			$new_password = random_string ( 'alnum', 8 );
			$admin ['password'] = sha1 ( $new_password );
			$this->save_admin ( $admin );

			$this->CI->email->from ( $this->CI->config->item ( 'email' ), $this->CI->config->item ( 'site_name' ) );
			$this->CI->email->to ( $email );
			$this->CI->email->subject ( $this->CI->config->item ( 'site_name' ) . ': Admin Password Reset' );
			$this->CI->email->message ( 'Your password has been reset to ' . $new_password . '.' );
			$this->CI->email->send ();
			return true;
		} else {
			return false;
		}
	}

	/**
	 * This function gets the admin by their email address and returns the values in an array it is not intended to be called outside this class
	 */
	private function get_admin_by_email($email) {
		$this->CI->db->select ( '*' );
		$this->CI->db->where ( 'email', $email );
		$this->CI->db->limit ( 1 );
		$result = $this->CI->db->get ( 'admin' );
		$result = $result->row_array ();

		if (sizeof ( $result ) > 0) {
			return $result;
		} else {
			return false;
		}
	}

	/**
	 * This function gets a complete list of all admin
	 */
	function get_admin_list() {
		$this->CI->db->select ( '*' );
		$this->CI->db->order_by ( 'lastname', 'ASC' );
		$this->CI->db->order_by ( 'firstname', 'ASC' );
		$this->CI->db->order_by ( 'email', 'ASC' );
		$result = $this->CI->db->get ( 'admin' );
		$result = $result->result ();

		return $result;
	}

	/**
	 * This function gets an individual admin
	 */
	function get_admin($id) {
		$this->CI->db->select ( '*' );
		$this->CI->db->where ( 'id', $id );
		$result = $this->CI->db->get ( 'admin' );
		$result = $result->row ();

		return $result;
	}
	function check_id($str) {
		$this->CI->db->select ( 'id' );
		$this->CI->db->from ( 'admin' );
		$this->CI->db->where ( 'id', $str );
		$count = $this->CI->db->count_all_results ();

		if ($count > 0) {
			return true;
		} else {
			return false;
		}
	}
	function check_email($str, $id = false) {
		$this->CI->db->select ( 'email' );
		$this->CI->db->from ( 'admin' );
		$this->CI->db->where ( 'email', $str );
		if ($id) {
			$this->CI->db->where ( 'id !=', $id );
		}
		$count = $this->CI->db->count_all_results ();

		if ($count > 0) {
			return true;
		} else {
			return false;
		}
	}
	function delete($id) {
		if ($this->check_id ( $id )) {
			$admin = $this->get_admin ( $id );
			$this->CI->db->where ( 'id', $id );
			$this->CI->db->limit ( 1 );
			$this->CI->db->delete ( 'admin' );

			return $admin->firstname . ' ' . $admin->lastname . ' has been removed.';
		} else {
			return 'The admin could not be found.';
		}
	}
}
