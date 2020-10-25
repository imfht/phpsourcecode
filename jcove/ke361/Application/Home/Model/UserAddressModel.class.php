<?php
namespace Home\Model;

use Think\Model;

class UserAddressModel extends Model
{
     protected $_auto = array(     
        array('user_id', UID, self::MODEL_INSERT), 
    );
    public function addressList(){
        if(UID){
            $where['user_id'] = UID;
            return $this->where($where)->select();
        }else {
            $this->error.='请先登录';
        }
    }
    public function addAdress($data){
        if(empty($data)){
            $data = $this->create();
        }
        if(isset($data['id']) && intval($data['id']) >0){
            if($this->info($data['id'])){
                $where['id'] = $data['id'];
                $res = $this->where($where)->save($data);
                return $res;
            }
        }else {
            return $this->add();
        }
    }
    public function setDefault($id){
        if(intval($id)>0){
            $where['status'] = 1;
            $where['user_id'] = UID;
            $data['status'] = 0;
            $this->where($where)->save($data);
            unset($data);
            unset($where);
            $where['id'] = $id;
            $data['status'] = 1;
           return  $this->where($where)->save($data);

        }
        $this->error.='无效的id';
        return false;
        
    }
    public function defaultAddress(){
        $where['status'] = 1;
        $where['user_id'] = UID;
        return $this->where($where)->find();
    }
    public function info($id){
        $where['id'] = $id;
        return $this->where($where)->find();
    }
}

?>