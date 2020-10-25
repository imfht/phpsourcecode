<?php
namespace Admin\Model;

use Think\Model;
class AdPositionModel extends Model
{
    protected $_auto  =   array(
        array('status', 1, self::MODEL_INSERT),
        array('create_time', NOW_TIME, self::MODEL_INSERT), 
    );
    protected $_validate = array(
        array('name', '', '标识已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
    );
    public function addAdPosition($ad){
        if($this->create($ad)){
            if(isset($ad['id']) && intval($ad['id']) > 0){
                return $this->save();
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
    public function getAdPositionByTemplate($template = 0){
        if($template == 0){
            $this->error = '无效的模版id';
        }
        $where['id'] = $template;
        return $this->where($where)->select();
    }
}

?>