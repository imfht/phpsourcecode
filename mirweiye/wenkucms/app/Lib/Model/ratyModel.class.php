<?php

class ratyModel extends RelationModel
{
    protected $_link = array(
        //关联角色
        'raty_user' => array(
            'mapping_type' => HAS_MANY,
            'class_name' => 'raty_user',
            'foreign_key' => 'id',
            'parent_key' => 'ratyid',
            'mapping_fields'=>'uid',
            'auto_prefix' => true
        )
    );
   
}