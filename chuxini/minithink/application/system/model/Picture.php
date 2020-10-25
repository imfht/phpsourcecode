<?php
namespace app\system\model;
/*
* 
* Created by PhpStorm.
* Author: 初心 [jialin507@foxmail.com]
* Date: 2017/5/5
*/
use think\Model;
class Picture extends Model{
    // 关闭自动写入update_time字段
    protected $updateTime = false;

    public function check_images($md5, $hash) {
        $data = ['md5'=>$md5, 'hash'=>$hash];
        $info = $this->where($data)->find();
        if(!empty($info)){
            return ['id'=>$info['id'], 'path'=>$info['path']];
        }
        return 0;
    }

}