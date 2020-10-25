<?php
namespace Admin\Model;
use Think\Model;

// 配置类型模型
class GroupModel extends CommonModel {
    protected $_validate = array(
        array('name','require','分组名必须'),
        array('name','','该分组名已经存在！',0,'unique',3),
        );

    protected $_auto		=	array(
        array('status',1,self::MODEL_INSERT,'string'),
        array('create_time','time',self::MODEL_INSERT,'function'),
        );
}
?>