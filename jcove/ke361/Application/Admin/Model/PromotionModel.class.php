<?php
namespace Admin\Model;

use Think\Model;

class PromotionModel extends Model
{
    /* 自动完成规则 */
    protected $_auto = array(
        array('end_time', 'strtotime', self::MODEL_BOTH, 'function'),
        array('start_time', 'strtotime', self::MODEL_BOTH, 'function'),
        array('status', 0, self::MODEL_INSERT),
    );
    public function info($id){
         if(empty($id) || $id <=0){
             $this->error.='无效的id';
             return -2;
         }
         $where['id'] = $id;
         return $this->where($where)->find();
     }
    
}

?>