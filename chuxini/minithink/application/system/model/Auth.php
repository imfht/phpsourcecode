<?php
namespace app\system\model;
/*
*
* Created by PhpStorm.
* Author: 初心 [jialin507@foxmail.com]
* Date: 2017/5/3
*/
use think\Model;
class Auth extends Model{
    // 关闭自动写入update_time字段
    protected $updateTime = false;

    public function getAuth($keyword = '', $start = 0, $limit = 10){
        $data = $this->where(['name'=>['like','%'.$keyword.'%']])->limit($start, $limit)->order('orders ASC')->select();

        return $data;
    }

    public function getPage($limit = 10){
        $page = $this->count();
        $page = floor($page / $limit);
        return $page;
    }

    public function getNodeAttr($value)
    {
        $data = explode(',',$value);
        return $data;
    }

    public function setCreateTimeAttr($value)
    {
        return $value && strtotime($value);
    }

}