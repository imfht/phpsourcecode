<?php
/*
* 
* Created by PhpStorm.
* Author: 初心 [jialin507@foxmail.com]
* Date: 2017/4/27
*/

namespace app\system\model;
use think\Model;
use think\Config as ConfigSettings;
class Config extends Model
{
    // 关闭自动写入update_time字段
    protected $updateTime = false;


    public function getConfig($keyword = '', $group = 0, $start = 0, $limit = 10){
        $where = [];
        $group && $where['group'] = $group;
        $keyword && $where['title|name'] = ['like','%'.$keyword.'%'];
        $data = $this->where($where)->limit($start, $limit)->order('orders ASC')->select();

        return $data;
    }

    public function getPage($group = 0, $limit = 10){
        $where = [];
        $group && $where['group'] = $group;

        $page = $this->where($where)->count();
        $page = floor($page / $limit);
        return $page;
    }
    public function setCreateTimeAttr($value)
    {
        return $value && strtotime($value);
    }
}