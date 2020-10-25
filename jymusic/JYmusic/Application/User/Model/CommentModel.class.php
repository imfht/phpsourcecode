<?php
// +----------------------------------------------------------------------
// | Author: 战神~~巴蒂
// +----------------------------------------------------------------------

namespace User\Model;
use Think\Model;


class CommentModel extends Model {
	protected $_map = array(         
		'con' 	=>'content',         
	);
	
    protected $_validate = array(
        array('content', 'require', '内容不能为空', self::MUST_VALIDATE ,'regex', self::MODEL_BOTH),
      	array('content', '5,255', '字段长度5-255字符', self::MUST_VALIDATE ,'length', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('comment_time', NOW_TIME, self::MODEL_INSERT),
        array('uid', UID, self::MODEL_INSERT),
        array('user','getName',3,'callback'),
        array('model_id', 2, self::MODEL_BOTH),
        array('status', 2, self::MODEL_BOTH),
    );
     protected  function getName () {
     	return get_nickName(UID);
    }
}
