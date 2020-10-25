<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 16-7-28
 * Time: 下午3:11
 * @author 大蒙<zzl@ourstu.com>
 */

namespace About\Model;


use Think\Model;

class FeedbackModel extends Model{

    public function setTrueDel($ids)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        $map['id']=array('in',$ids);
        $res=$this->where($map)->delete();
        return $res;
    }
} 