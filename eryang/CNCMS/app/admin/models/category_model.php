<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * 后台类别管理模型
 *
 * @category Models
 * @author 二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class Category_model extends Base_model {

    var $model_table='category';

	function __construct() {
		parent::__construct ();
	}
	
	// ------------------------------------------------------------------------


    /**
     * 获得所有类别
     *
     * @access public
     * @return object
     */
    public function get_categorys($rows, $page) {
        if(!empty($rows))
        {
            $this->db->limit($rows);
        }

        if(!empty($page))
        {
            $this->db->offset($page);
        }
        return $this -> db -> select('c1.id as id, c1.name as name ,(select c2.name from ' . $this -> db -> dbprefix($this->model_table.' as c2') . ' where c2.id=c1.pid ) as pname,c1.rank as rank,c1.status as status ') -> order_by('id asc,rank asc') -> get($this -> db -> dbprefix($this->model_table.' as c1')) -> result_array();
    }

    // ------------------------------------------------------------------------

    /**
	 * 获取类别数据并实现分类树数据
	 *
	 * @access public
	 * @param
	 *        	array 条件参数集
	 * @return array 处理后形成的一维数据数组
	 */
	function category_datas($arg = array()) {
		$this->load->helper ( 'my_tree' );
		isset ( $arg ['status'] ) ? $this->db->where ( 'status', $arg ['status'] ) : '';
        return classify_tree ( $this->db->select ( '*' )->from ($this -> db -> dbprefix($this->model_table) )->order_by ( 'rank', 'asc' )->get ()->result_array () );
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 获取父级下拉菜单数据
     *
	 * @access public
	 * @return object
	 */
	public function get_pid_data() {
        return $this->db->select ( 'id, name' )->where ( 'pid =', 0)->where ( 'status =', 1 )->order_by ( 'rank asc' )->get ( $this->db->dbprefix ( $this->model_table ) )->result ();
	}
	
	// ------------------------------------------------------------------------

    /**
     * 获取子级下拉菜单数据
     *
     * @access public
     * @return object
     */
    public function get_children_data() {
        return $this->db->select ( 'id, name' )->where ( 'pid <>', 0)->where ( 'status =', 1 )->order_by ( 'rank asc' )->get ( $this->db->dbprefix ( $this->model_table ) )->result ();
    }

    // ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------

/* End of file category_model.php */
/* Location: ./app/admin/models/category_model.php */
