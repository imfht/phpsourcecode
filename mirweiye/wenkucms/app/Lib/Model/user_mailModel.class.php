<?php
class user_mailModel extends RelationModel
{
	
protected $_auto =array(

array('add_time','time',1,'function'), // 对create_time字段在更新的时候写入当前时间戳

);


protected $_link = array(
        //关联角色
        'user' => array(
            'mapping_type' => BELONGS_TO,
            'class_name' => 'user',
            'foreign_key' => 'fromid',
            'parent_key' => 'uid',
            'as_fields'=>'username',
            'auto_prefix' => true
        ),
           );

    
}