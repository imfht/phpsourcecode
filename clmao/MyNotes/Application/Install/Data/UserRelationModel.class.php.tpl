<?php

/*
  用户与角色管理模型
 */

namespace Admin\Model;

use Think\Model\RelationModel;

class UserRelationModel extends RelationModel {
    Protected $tableName = 'user';
    protected $_link = array(
        'role' => array(
            'mapping_type' => self::MANY_TO_MANY,
            'class_name' => 'role',
            'mapping_name' => 'role',
            'foreign_key' => 'user_id',
            'relation_foreign_key' => 'role_id',
            'mapping_fields' => 'id,name,remark',
            'relation_table' => '[clmao_]role_user',
        ),
    );
    
  
}

?>