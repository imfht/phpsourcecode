<?php
namespace Admin\Model;
use Think\Model;

// 角色模型
class MroleModel extends CommonModel {
    public $_validate = array(
        array('name','require','组名必须'),
        array('name','','该组名已经存在！',0,'unique',3),
        array('score','number','积分必须为数字！',1),
        array('score','require','积分必须'),
        );

    public $_auto		=	array(
        array('create_time','time',3,'function'),
        array('update_time','time',3,'function'),
        );
        
        
   

    protected function fieldFormat(&$value) {//格式化数组中的元素
        if(is_int($value)) {
            $value = intval($value);
        } else if(is_float($value)) {
            $value = floatval($value);
        }else if(is_string($value)) {
            $value = addslashes($value);
        }
        return $value;
    }
	


	
}
?>