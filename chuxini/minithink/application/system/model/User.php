<?php
namespace app\system\model;
/*
* 
* Created by PhpStorm.
* Author: 初心 [jialin507@foxmail.com]
* Date: 2017/5/2
*/
use think\Model;

class User extends Model {

    public function getUser($keyword = '', $start = 0, $limit = 10){
        $data = $this->where(['username'=>['like','%'.$keyword.'%']])->limit($start, $limit)->order('orders ASC')->select();

        return $data;
    }

    public function save_user($data) {
        if($data['id']){
            //修改
            if($data['password']){
                $data['password'] = auth_password($data['password']);
            }else{
                unset($data['password']);
            }
        }else{
            //新增
            $data['password'] = auth_password($data['password']);
        }

        unset($data['update_time']);
        return $this->allowField(true)->save($data,$data['id']);
    }

    public function getPage($limit = 10){
        $page = $this->count();
        $page = floor($page / $limit);
        return $page;
    }

    public function setCreateTimeAttr($value)
    {
        return $value && strtotime($value);
    }

    /**
     * 获取角色名称
     * @param $value
     * @return mixed
     */
    public function getRoleAttr($value)
    {
        $auth = Auth::get($value);
        return $auth['name'];
    }
}