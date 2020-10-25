<?php
namespace Home\Model;

use Think\Model;

class OrderModel extends Model
{
    protected $_auto = array(
        array('uid', UID, self::MODEL_INSERT),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('order_no', 'create_rand_num', self::MODEL_BOTH, 'function', 12),
    );
    
    public function addOrder($order){
        if(empty($order)){
            $this->error.='无效的订单信息';
            return ;
        }
        if($this->create($order)){
            return $this->add();
        }else {
            return ;
        }
    }
}

?>