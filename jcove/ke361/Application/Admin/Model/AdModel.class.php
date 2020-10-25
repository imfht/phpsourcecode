<?php
namespace Admin\Model;

use Think\Model;

class AdModel extends Model
{
    protected $_auto  =   array(
        array('status', 1, self::MODEL_INSERT),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
    );
    public function addAd($ad){
        if($this->create($ad)){
            if(isset($ad['id']) && intval($ad['id']) > 0){
                $where['id'] = $ad['id'];
                return $this->where($where)->save();
            }
            return $this->add();
        }else{
            return false;
        }
    }
    public function info($id){
        $where['id']    =   $id;
        return $this->where($where)->find(); 
    }
}

?>