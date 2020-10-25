<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * 后台首页幻灯片管理模型
 *
 * @category Models
 * @author 二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class Slide_model extends Base_model {

    var $model_table='slide';

	function __construct() {
		parent::__construct ();
	}

	// ------------------------------------------------------------------------


    /**
     * 获得所有首页幻灯片
     *
     * @access public
     * @return object
     */
    public function get_slides($rows, $page) {
        if(!empty($rows))
        {
            $this->db->limit($rows);
        }

        if(!empty($page))
        {
            $this->db->offset($page);
        }
        $table_slide = $this->db->dbprefix ( $this->model_table );
        $table_mode = $this->db->dbprefix ( 'mode' );
        return $this -> db -> select("$table_slide.id as id, $table_slide.title as title ,$table_mode.name as mode,$table_slide.url as url,
                                     $table_slide.thumb as thumb,$table_slide.rank as rank,$table_slide.remark as remark,
                                     $table_slide.addtime as addtime,$table_slide.updatetime as updatetime,
                                    $table_slide.addip as addip,$table_slide.updateip as updateip,
                                    $table_slide.addip_address as addip_address,$table_slide.updateip_address as updateip_address,$table_slide.status as status ")
            -> from($table_slide)->join($table_mode,"$table_mode.id = $table_slide.mode_id",'left')
            ->order_by('id desc,addtime desc,updatetime desc') ->get()->result_array();
    }

    // ------------------------------------------------------------------------


}

// ------------------------------------------------------------------------

/* End of file slide_model.php */
/* Location: ./app/admin/models/slide_model.php */
