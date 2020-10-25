<?php
// +----------------------------------------------------------------------
// | Author: 战神~~巴蒂
// +----------------------------------------------------------------------

namespace User\Model;
use Think\Model;


class UserSpaceModel extends Model {
	protected $_map = array(         
  
	);
	
    protected $_validate = array(
        array('name', 'require', '名称不能为空', self::MUST_VALIDATE ,'regex', self::MODEL_BOTH),
        array('album_pic', 'require', '图片地址不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
	);

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('uid', UID, self::MODEL_INSERT),
		array('bg','text_filter',3,'callback'),
		array('channel','text_filter',3,'callback'),
		array('indexunit','text_filter',3,'callback'),
		array('sidebarunit','text_filter',3,'callback'),
		array('skin','text_filter',3,'callback'),
		array('title','text_filter',3,'callback'),
    );

}
