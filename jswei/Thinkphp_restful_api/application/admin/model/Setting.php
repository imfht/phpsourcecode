<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/4/24
 * Time: 8:54
 */

namespace app\admin\model;

use think\Model;

class Setting extends Model{
    protected $pk = 'id';
    protected $table="think_config";
    protected $autoWriteTimestamp = true;

    protected function getStatusAttr($value){
        return $value?true:false;
    }

    protected function setUpdateTimeAttr(){
        return time();
    }

    protected function setCreateTimeAttr(){
        return time();
    }
}