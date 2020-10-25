<?php
/**
 *
 */

namespace Common\Model;

use Think\Model;

class ActionLimitModel extends Model
{
    protected $tableName = 'action_limit';
    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
    );


    public function addActionLimit($data)
    {
        $res = $this->add($data);
        return $res;
    }

    public function getActionLimit($where){
        $limit = $this->where($where)->find();
        return $limit;
    }

    public function getList($where){
        $list = $this->where($where)->select();
        return $list;
    }


    public function editActionLimit($data)
    {
        $res = $this->save($data);
        return $res;
    }


}















