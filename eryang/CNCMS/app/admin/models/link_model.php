<?php
if (! defined ( 'BASEPATH' ))
    exit ( 'No direct script access allowed' );

/**
 * 后台友情链接管理模型
 *
 * @category Models
 * @author 二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class Link_model extends Base_model {

    var $model_table='link';

    function __construct() {
        parent::__construct ();
    }

    // ------------------------------------------------------------------------


    /**
     * 获得所有友情链接
     *
     * @access public
     * @return object
     */
    public function get_links($rows, $page) {
        if(!empty($rows))
        {
            $this->db->limit($rows);
        }

        if(!empty($page))
        {
            $this->db->offset($page);
        }
        $table_link = $this->db->dbprefix ( $this->model_table );
        return $this -> db -> select(' * ')->from($table_link)->order_by('rank asc') ->get()->result_array();
    }

    // ------------------------------------------------------------------------


}

// ------------------------------------------------------------------------

/* End of file link_model.php */
/* Location: ./app/admin/models/link_model.php */
