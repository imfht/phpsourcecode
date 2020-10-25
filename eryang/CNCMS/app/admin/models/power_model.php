<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * 后台权限管理模型
 *
 * @category Models
 * @author 二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class Power_model extends Base_model {

    var $model_table='power';

	function __construct() {
		parent::__construct ();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 获取权限数据并实现分类树数据
	 *
	 * @access public
	 * @param
	 *        	array 条件参数集
	 * @return array 处理后形成的一维数据数组
	 */
	function power_datas($arg = array()) {
		$this->load->helper ( 'my_tree' );
		isset ( $arg ['status'] ) ? $this->db->where ( 'status', $arg ['status'] ) : '';
        return classify_tree ( $this->db->select ( '*' )->from ($this->db->dbprefix ( $this->model_table ) )->order_by ( 'rank', 'asc' )->get ()->result_array () );
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 获取父级下拉菜单数据
	 *
	 * @access public
	 * @return object
	 */
	public function get_pid_data() {
		return $this->db->select ( 'id, name' )->where ( 'pid =', 1 )->where ( 'status = ', 1 )->order_by ( 'rank asc' )->get (  $this->db->dbprefix ( $this->model_table ) )->result ();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 添加权限
	 *
	 * @access public
	 * @param
	 *        	array 数据数组
	 * @return boolean 成功与否
	 */
	function add($save) {
		if ($this->get_one ( $this->model_table, array (
				'name' => $save ['name'] 
		) )) {
			return FALSE;
		}
		$rank = $this->db->select_max ( 'rank' )->from ($this->db->dbprefix ( $this->model_table ) )->where ( array (
				'pid' => $save ['pid'] 
		) )->get ()->row_array ();
		$save ['rank'] = $rank ['rank'] + 1;
		$insert_id = $this->insert ( $this->model_table, $save );
		if ($insert_id) {
			$super_power_group = $this->get_one ( 'role', array (
					'id' => 1 
			) );
			$this->update ( 'role', array (
					'powers' => $super_power_group ['powers'] . ',' . $insert_id 
			), array (
					'id' => 1 
			) );
			return TRUE;
		}
		return FALSE;
	}
	// ------------------------------------------------------------------------
	
	/**
	 * 编辑权限
	 *
	 * @access public
	 * @param
	 *        	array 数据数组
	 * @param
	 *        	number 数据编号
	 * @return array 数据更新情况
	 */
	function edit($save, $edit_id) {
		$p_data = $this->get_one ( $this->model_table , array (
				'id' => $save ['pid'] 
		) );
		if ($p_data && $p_data ['status'] != 1) {
			$save ['status'] = $p_data ['status'];
		}
		if ($save ['status'] != 1) {
			$this->load->helper ( 'my_children' );
			$children_ids = children_ids ( $edit_id, $this->model_table );
			if ($children_ids) {
				$this->db->where_in ( 'id', $children_ids )->update (  $this->model_table , array (
						'status' => $save ['status'] 
				) );
			}
		}
		$return = array (
				'affected_rows' => $this->update ($this->model_table , $save, array (
						'id' => $edit_id 
				) ),
				'status' => $save ['status'] 
		);
		return $return;
	}
	// ------------------------------------------------------------------------
	
	/**
	 * 删除权限所在权限组的数据
	 *
	 * @access public
	 * @param
	 *        	number 数据编号
	 */
	function del($del_id) {
		$group_datas = $this->get_all ( 'role' );
		foreach ( $group_datas as $data ) {
			$power = explode ( ',', $data ['powers'] );
			$find_key = array_search ( $del_id, $power );
			if ($find_key) {
				if ($find_key !== '') {
					unset ( $power [$find_key] );
					$this->update ( 'role', array (
							'powers' => implode ( ',', $power ) 
					), array (
							'id' => $data ['id'] 
					) );
				}
			}
		}
	}
	
	// ------------------------------------------------------------------------
}

/* End of file power_model.php */
/* Location: ./app/admin/models/power_model.php */
