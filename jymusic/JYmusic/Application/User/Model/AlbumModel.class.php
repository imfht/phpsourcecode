<?php
// +----------------------------------------------------------------------
// | Author: 战神~~巴蒂
// +----------------------------------------------------------------------

namespace User\Model;
use Think\Model;


class AlbumModel extends Model {
	protected $_map = array(         
		'alb' 	=>'name',       
		'img_url'  	=>'album_pic',		
		'singer'	=>'singer_name',
		'pub' 		=>'pub_time',  
	);
	
    protected $_validate = array(
        array('name', 'require', '名称不能为空', self::MUST_VALIDATE ,'regex', self::MODEL_BOTH),
        array('album_pic', 'require', '图片地址不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('singer_name', 'require', '所属歌手不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        //array('genre_name', 'require', '所属曲风不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('add_time', NOW_TIME, self::MODEL_INSERT),
        array('up_uid', UID, self::MODEL_INSERT),
        array('up_uname','getName',3,'callback'), 
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', '-1', self::MODEL_BOTH),
    );
     protected  function getName () {
     	return get_userName(UID);
    }


}
