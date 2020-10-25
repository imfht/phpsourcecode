<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * 后台角色管理模型
 *
 * @category Models
 * @author 二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class Role_model extends Base_model {

    var $model_table='role';

	function __construct() {
        parent::__construct ();
	}

	// ------------------------------------------------------------------------
	
	/**
	 * 角色数据
     *
	 * @access public
	 * @return array 数据数组
	 */
	function role_datas($rows, $page) {
		$datas = $this->get_all_page ($this->model_table,$rows,$page );
		foreach ( $datas as $n => $data ) {
			$datas [$n] ['use_count'] = $this->db->where ( 'role_id', $data ['id'] )->count_all_results ( 'manager' );
		}
		return $datas;
	}

	// ------------------------------------------------------------------------

}

/* End of file role_model.php */
/* Location: ./app/admin/models/role_model.php */
