<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * 后台模块管理模型
 *
 * @category Models
 * @author 二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class Mode_model extends Base_model {

    var $model_table='mode';

	function __construct() {
		parent::__construct ();
	}
	
	// ------------------------------------------------------------------------


    /**
     * 获取模块下拉菜单数据
     *
     * @access public
     * @return object
     */
    public function get_mode_data($name=fasle) {
        return $this->db->select ( 'id, name' )->where ( 'name', $name)->where ( 'status =', 1 )->order_by ( 'rank asc' )->get ( $this->db->dbprefix ( $this->model_table ) )->result ();
    }

    // ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------

/* End of file category_model.php */
/* Location: ./app/admin/models/category_model.php */
