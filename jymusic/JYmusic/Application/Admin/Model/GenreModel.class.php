<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;


class GenreModel extends Model {
    protected $_validate = array(
        array('name', 'require', '名称不能为空', self::MUST_VALIDATE ,'regex', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('add_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', 1, self::MODEL_BOTH),
		array('cover_url', 'getcover', self::MODEL_BOTH,'callback'),
    );


	function getcover() {
		$url = $_POST['cover_url'];
   		if(!empty($url)) {
			return $url;
		}else{
			return __ROOT__.'/Uploads/Picture/album_cover.jpg';
		}
    }

}
