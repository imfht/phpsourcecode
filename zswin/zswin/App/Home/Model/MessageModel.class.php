<?php
namespace Home\Model;
use Think\Model;


class MessageModel extends Model {
   
  
   protected $_validate = array(
        array('content','require','内容必须！'), //默认情况下用正则进行验证
     
        
    );

  
       protected $_auto = array(
        array('is_read', '0', 1),
         array('status', '1', 1),
        array('create_time', NOW_TIME, 1),
        );

   
    
   
}
