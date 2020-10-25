<?php

    class ContentModel extends RelationModel{
        
        Protected $tableName='';
        
        Protected $_link=array(
            'category'=>array(
                'mapping_type'=>BELONGS_TO,
                'foreign_key'=>'cid',
                'class_name'=>'category',
                'mapping_name'=>'category',
                'mapping_fields'=>'title,module,list,content',
                'as_fields'=>'title:ttitle,module,list,content:content_t'
            ),
            'member'=>array(
                'mapping_type'=>BELONGS_TO,
                'foreign_key'=>'user',
                'class_name'=>'member',
                'mapping_name'=>'member'
            ),
             'user'=>array(
                'mapping_type'=>BELONGS_TO,
                'foreign_key'=>'user',
                'class_name'=>'user',
                'mapping_name'=>'user'
            ),
        );
    }
?>