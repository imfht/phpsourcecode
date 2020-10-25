<?php
// +----------------------------------------------------------------------
// | Author: 战神巴蒂<378020023@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;


class AlbumModel extends Model {
    protected $_validate = array(
        array('name', 'require', '名称不能为空', self::MUST_VALIDATE ,'regex', self::MODEL_BOTH),
        array('artist_name', 'require', '所属艺术家不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
    );

    protected $_auto = array(
    	array('sort','getSort', self::MODEL_BOTH,'callback'),
        array('add_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_BOTH),
        array('genre_name', 'getGnreName', self::MODEL_BOTH,'callback'),
		array('cover_url', 'getcover', self::MODEL_BOTH,'callback'),
    );
    
    function getGnreName() {    	
    	return get_genre_name($_POST['genre_id']);
    }
    
	function getSort() {
   		   	
    	return !empty($_POST['sort'])? $_POST['sort'] : '0' ;
    }

    function getcover() {
		$url = $_POST['cover_url'];
   		if(!empty($url)) {
			return $url;
		}else{
			return __ROOT__.'/Uploads/Picture/album_cover.jpg';
		}
    }
}
