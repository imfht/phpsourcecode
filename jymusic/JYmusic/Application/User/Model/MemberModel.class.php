<?php
// +----------------------------------------------------------------------
// | Author: 战神~~巴蒂
// +----------------------------------------------------------------------

namespace User\Model;
use Think\Model\RelationModel;


class MemberModel extends RelationModel {
	
    protected $_validate = array(
        array('nickname', 'require', '昵称不能为空', self::MUST_VALIDATE ,'regex', 1),
        //array('nickname','','昵称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
      	array('nickname', '2,12', '昵称长度2-12字符', self::MUST_VALIDATE ,'length', 3),
      	array('nickname','checknickname','昵称已经存在！',0,'function'), // 自定义函数验证密码格式
      	array('qq', 'number', 'qq号只能为数字', self::MUST_VALIDATE ,'regex', 3),
      	array('password','checkPwd','密码格式不正确',0,'function'), // 自定义函数验证密码格式
      	array('signature', '5,500', '签名长度5-500字符', self::MUST_VALIDATE ,'length', 3),
    );

    protected $_auto = array(
        array('status', 1, self::MODEL_BOTH),
    );
    
	protected $_link = array(
	    'Dept' => array(    
	    	'mapping_type'  => self::HAS_MANY,    
	    	'class_name'    => 'Songs',    
	    	'foreign_key'   => 'up_uid',
	    	'parent_key'    => 'uid',
	    	'mapping_name'  => 'music', 
	    	'mapping_fields'=> 'id,name',
	    	'mapping_limit' => 3,
	    	'mapping_order'=> 'add_time DESC',
	    ),
    );
	    

}
