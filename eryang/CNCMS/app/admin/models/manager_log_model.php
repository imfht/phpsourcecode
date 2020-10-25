<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * 后台系统日志管理模型
 *
 * @category Models
 * @author 二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class Manager_log_model extends Base_model {

    var $model_table='manager_logging';

	function __construct() {
		parent::__construct ();
	}

	// ------------------------------------------------------------------------


    /**
     * 获得所有系统日志
     *
     * @access public
     * @return object
     */
    public function get_manager_logs($rows, $page) {
        $table_manager_logging = $this->db->dbprefix ( $this->model_table);
        $table_role = $this->db->dbprefix ( 'role' );
        if(!empty($rows))
        {
            $this->db->limit($rows);
        }

        if(!empty($page))
        {
            $this->db->offset($page);
        }
        return $this -> db -> select("$table_manager_logging.id as id, $table_manager_logging.username as username ,$table_role.name as role,$table_manager_logging.activity as activity,$table_manager_logging.url as url,$table_manager_logging.time as time,$table_manager_logging.ip as ip,$table_manager_logging.ip_address as ip_address  ") -> from($table_manager_logging)->join($table_role,"$table_role.id = $table_manager_logging.role_id",'left') ->order_by('id  desc') ->get()->result_array();
    }

    // ------------------------------------------------------------------------



}

// ------------------------------------------------------------------------

/* End of file manager_log_model.php */
/* Location: ./app/admin/models/manager_log_model.php */
