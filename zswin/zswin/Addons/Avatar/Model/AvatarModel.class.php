<?php

namespace Addons\Avatar\Model;
use Think\Model;

class AvatarModel extends Model {
    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
    );

    public function getAvatar($uid) {
        //查询数据库
        $map = array('uid'=>$uid, 'status'=>1, 'is_temp'=>0);
        $avatar = $this->where($map)->find();
        //返回结果
        return $avatar['path'];
    }

    public function getTempAvatar($uid) {
        //查询数据库
        $map = array('uid'=>$uid, 'status'=>1, 'is_temp'=>1);
        $avatar = $this->where($map)->find();
        //返回查询结果
        return $avatar['path'];

    }

    public function saveAvatar($uid, $path) {
        //删除旧头像和临时头像
        $this->removeAvatar($uid);
        $this->removeTempAvatar($uid);

        //保存新头像
        $data = array(
            'uid'=>$uid,
            'path'=>$path,
            'status'=>1,
            'is_temp'=>0,
        );
        $data = $this->create($data);
        clean_query_user_cache($uid,array('avatar32', 'avatar64', 'avatar128', 'avatar256'));
        return $this->add($data);
    }

    public function saveTempAvatar($uid, $path) {
        //删除旧的临时头像
        $this->removeTempAvatar($uid);

        //保存新的临时头像
        $data = array(
            'uid'=>$uid,
            'path'=>$path,
            'status'=>1,
            'is_temp'=>1,
        );
        $data = $this->create($data);
        return $this->add($data);
    }

    public function removeAvatar($uid) {
        //TODO 删除头像文件
        //删除数据库记录
        $map = array('uid'=>$uid,'is_temp'=>0);
         $avatar = $this->where($map)->find();
         
         
         $temp=explode('.', $avatar['path']);
         $hz=$temp[count($temp)-1];
         $filename=str_replace('.'.$hz, '', $avatar['path']);
         
         
         
         
         unlink('./Uploads/Avatar/'.$avatar['path']);
         unlink('./Uploads/Avatar/'.$filename.'_64_64.'.$hz);
         unlink('./Uploads/Avatar/'.$filename.'_128_128.'.$hz);
         
         
        return $this->where($map)->delete();
    }

    public function removeTempAvatar($uid) {
        //TODO 删除头像文件
       
    	
    	
        //删除数据库记录
        $map = array('uid'=>$uid,'is_temp'=>1);
        $avatar = $this->where($map)->find();
         unlink('./Uploads/Avatar/'.$avatar['path']);
        return $this->where($map)->delete();
    }
}