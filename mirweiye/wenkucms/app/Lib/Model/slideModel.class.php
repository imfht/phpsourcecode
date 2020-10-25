<?php
# 焦点图
class slideModel extends Model
{
	//自动完成
    protected $_auto = array(
        array('add_time', 'time', 1, 'function'),
    );
    //自动验证
    protected $_validate = array(
        array('name', 'require', '标题为空'),
    );
}