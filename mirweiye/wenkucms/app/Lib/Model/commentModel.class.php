<?php
class commentModel extends RelationModel
{
    //自动完成
    protected $_auto = array(
        array('add_time', 'time', 1, 'function'),
      
    );
    
}