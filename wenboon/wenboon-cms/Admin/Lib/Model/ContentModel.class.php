<?php

    class ContentModel extends RelationModel{
        
        public $tableName='';
        
        public $_link=array(
            'category'=>array(
                'mapping_type'=>BELONGS_TO,
                'foreign_key'=>'cid',
                'class_name'=>'category',
                'mapping_name'=>'category'
            )
        );
        
        
    }
?>