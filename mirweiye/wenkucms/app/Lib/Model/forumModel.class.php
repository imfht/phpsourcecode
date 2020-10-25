<?php
class forumModel extends RelationModel
{
    //自动完成
    protected $_auto = array(
        array('add_time', 'time', 1, 'function'),
    );
    //自动验证
    protected $_validate = array(
        array('title', 'require', '标题为空'),
        array('cateid', 'require', '请选择板块'),
        array('content', 'require', '请填写内容'),
    );
    
}