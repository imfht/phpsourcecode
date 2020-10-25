<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
/**
 * 后台管理员操作模型
 *
 * @category Models
 * @author 二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class Manager_model extends Base_model {

    var $model_table='manager';

	/**
	 * 构造函数
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		parent::__construct ();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 获取管理员的信息
	 *
	 * @access public
	 * @param
	 *        	mixed
	 * @return object
	 */
	function get_managers($rows, $page) {

		$table_manager = $this->db->dbprefix ($this->model_table );
		$table_role = $this->db->dbprefix ( 'role' );
        if(!empty($rows))
        {
            $this->db->limit($rows);
        }

        if(!empty($page))
        {
            $this->db->offset($page);
        }
		return $this->db->select ( "$table_manager.id, $table_manager.username, $table_manager.password, $table_manager.nickname, $table_manager.phone, $table_manager.email,$table_manager.role_id,$table_manager.status,$table_role.name as rolename,$table_role.powers,$table_manager.status,$table_manager.skin,$table_role.introduce,$table_manager.last_log_time,$table_manager.now_log_time" )->from ( $table_manager )->join ( $table_role, "$table_role.id = $table_manager.role_id",'left' )->get ()->result_array ();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 获取管理员的信息
	 *
	 * @access public
	 * @param
	 *        	mixed
	 * @return object
	 */
	function get_manager_by_username($username = '', $type = 'username') {
		$table_manager = $this->db->dbprefix ( $this->model_table );
		$table_role = $this->db->dbprefix ( 'role' );
		if ($type == 'id') {
			$this->db->where ( $table_manager . '.id', $username );
		} else {
            $this->db->where ( $table_manager . '.username', strtolower($username) );
		}
		return $this->db->select ( "$table_manager.id, $table_manager.username, $table_manager.password, $table_manager.nickname, $table_manager.phone, $table_manager.email,$table_manager.role_id,$table_manager.status,$table_role.name as rolename,$table_role.powers,$table_manager.status,$table_manager.skin,$table_role.introduce,$table_manager.last_log_time,$table_manager.now_log_time" )->from ( $table_manager )->join ( $table_role, "$table_role.id = $table_manager.role_id" )->get ()->row ();
	}
	
    // ------------------------------------------------------------------------

}

/* End of file manager_model.php */
/* Location: ./app/admin/models/manager_model.php */
