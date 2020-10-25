<?php
// +----------------------------------------------------------------------
// | @JYmusic
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;


class MessageModel extends Model {
    protected $_validate = array(
        array('title', 'require', '标题不能为空', self::MUST_VALIDATE ,'regex', self::MODEL_BOTH), 
        array('content', 'require', '内容不能为空', self::MUST_VALIDATE ,'regex', self::MODEL_BOTH),       
    );

    protected $_auto = array(
        array('post_time', NOW_TIME, self::MODEL_INSERT),
        array('model_id', 1, self::MODEL_INSERT),
        //array('post_user_id','getUserId',3,'callback'), // 对post_user_id字段在新增和编辑的时候回调getName方法
        array('status',1,self::MODEL_INSERT), // 
    );
    

}
