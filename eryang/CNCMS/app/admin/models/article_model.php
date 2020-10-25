<?php
if (! defined ( 'BASEPATH' ))
    exit ( 'No direct script access allowed' );

/**
 * 后台文章管理模型
 *
 * @category Models
 * @author 二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class Article_model extends Base_model {

    var $model_table='article';

    function __construct() {
        parent::__construct ();
    }

    // ------------------------------------------------------------------------


    /**
     * 获得所有文章
     *
     * @access public
     * @return object
     */
    public function get_articles($status=1,$rows, $page) {

        if(!empty($rows))
        {
            $this->db->limit($rows);
        }

        if(!empty($page))
        {
            $this->db->offset($page);
        }

        $table_article = $this->db->dbprefix ( $this->model_table );
        $table_category = $this->db->dbprefix ( 'category' );
        return $this -> db -> select("$table_article.id as id, $table_article.title as title ,$table_category.name as category,$table_article.addtime as addtime,$table_article.addip as addip,$table_article.updatetime as updatetime,$table_article.updateip as updateip,$table_article.writer as writer,$table_article.status as status ") -> from($table_article)->join($table_category,"$table_category.id = $table_article.categoryid",'left') -> where("$table_article.status = ",$status)->order_by('id desc,addtime desc,updatetime desc') ->get()->result_array();
    }

    // ------------------------------------------------------------------------


}

// ------------------------------------------------------------------------

/* End of file article_model.php */
/* Location: ./app/admin/models/article_model.php */
