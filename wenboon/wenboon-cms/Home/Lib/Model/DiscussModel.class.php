<?php

    class DiscussModel extends RelationModel{
        
        Protected $_link=array(
            'member'=>array(
                'mapping_type'=>BELONGS_TO,
                'foreign_key'=>'user',
                'class_name'=>'member',
                'mapping_name'=>'user'
            ),
        );
    }
?>