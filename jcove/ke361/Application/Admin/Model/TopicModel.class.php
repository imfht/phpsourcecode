<?php
namespace Admin\Model;

use Think\Model;

class TopicModel extends Model
{
    protected $_auto = array(
         array('status', 1, self::MODEL_INSERT),
    );
}

?>