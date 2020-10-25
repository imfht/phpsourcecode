<?php
namespace Admin\Model;

use Think\Model;
use Admin\Controller\PublicController;

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
    public function canDeliver($id){
        if(empty($id)){
            $this->error.='无效的id';
            return false;
        }
        $where['id'] = $id;
        $order = $this->field('status')->where($where)->find();
        $status = $order['status'];
       
        switch ($status){
           case -1 :
                $this->error.='订单已删除';
                return false;
           case 0 :
                $this->error.='订单未付款';
                return false;
           case 1 :
                
                return true;
           case 2 :
                $this->error.='订单已发货';
                return false;
           case 3 :
                $this->error.='订单已完成';
                return false;
           default:
               $this->error.='未知错误';
               return false;
        }   
    }
    public function canPaid($id){
        if(empty($id)){
            $this->error.='无效的id';
            return false;
        }
        $where['id'] = $id;
        $order = $this->field('status')->where($where)->find();
        $status = $order['status'];
         
        switch ($status){
            case -1 :
                $this->error.='订单已删除';
                return false;
            case 0 :
                return true;
            case 1 :
                $this->error.='订单已付款';
                return false;
                
            case 2 :
                $this->error.='订单已发货';
                return false;
            case 3 :
                $this->error.='订单已完成';
                return false;
            default:
                $this->error.='未知错误';
                return false;
        }
    }
    public function deliver($id){
        if(empty($id)){
            $this->error.='无效的id';
            return false;
        }
        $where['id'] = $id;
        $data['status'] = 2;
        return $this->where($where)->save($data);
    }
    public function changeField($id,$field,$value){
        
        if(empty($id)||empty($field)||empty($value)){
            $this->error.='id,字段名,值不能为空';
            return false;
        }
        $where['id'] = $id;
        $data[$field]= $value;
        return $this->where($where)->save($data);
        
    }
}

?>