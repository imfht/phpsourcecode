<?php
    class AdvertisementModel extends RelationModel{
        public $_link=array(
            'advertext'=>array(
                'mapping_type'=>HAS_MANY,
                'foreign_key'=>'pid',
                'class_name'=>'advertext',
                'mapping_name'=>'child'
            )
        );
        
        
    }
?>