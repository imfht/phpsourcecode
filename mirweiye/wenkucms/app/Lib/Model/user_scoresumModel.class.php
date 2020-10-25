<?php

class user_scoresumModel extends RelationModel
{
   
    protected $_link = array(
        
        'username' => array(
            'mapping_type' => BELONGS_TO,
            'class_name' => 'user',
            'foreign_key' => 'uid',
            'parent_key' => 'uid',
            'as_fields'=>'username',
             'auto_prefix' => true
        )
    );
    
}