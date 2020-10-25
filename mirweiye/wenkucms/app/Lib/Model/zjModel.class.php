<?php
class zjModel extends RelationModel
{
    //自动完成
    protected $_auto = array(
        array('add_time', 'time', 1, 'function'),
    );
    //自动验证
    protected $_validate = array(
        array('title', 'require', '专辑标题不能为空'),
        //array('cateid', 'require', '未选择竞猜主题分类'),
        array('score','number','必须为数字！'),
        
        
    );

}