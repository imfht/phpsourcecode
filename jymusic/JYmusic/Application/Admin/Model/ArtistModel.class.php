<?php
// +----------------------------------------------------------------------
// | Author: 战神巴蒂<378020023@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;


class ArtistModel extends Model {
    protected $_validate = array(
        array('name', 'require', '名称不能为空', self::MUST_VALIDATE ,'regex', self::MODEL_BOTH),
    );

    protected $_auto = array(
    	array('sort','getSort', self::MODEL_BOTH,'callback'),
        array('add_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', 1, self::MODEL_INSERT),
		array('cover_url', 'getcover', self::MODEL_BOTH,'callback'),
    );
    
   	function getSort() {
   		   	
    	return !empty($_POST['sort'])? $_POST['sort'] : '0' ;
    }

	function getcover() {
		$url = $_POST['cover_url'];
   		if(!empty($url)) {
			return $url;
		}else{
			return __ROOT__.'/Uploads/Picture/artist_cover.jpg';
		}
    }
    

}
