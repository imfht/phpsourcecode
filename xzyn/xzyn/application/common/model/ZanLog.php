<?php
namespace app\common\model;
// 赞记录模型
use think\Model;

class ZanLog extends Model {
	// 新增自动完成列表
    protected $insert = ['uid'];


    public function User() {	//关联用户表
        return $this->hasOne('User', 'id', 'uid')->field('username, name');
    }

    protected function setUidAttr($value) {	//uid字段[修改器]
        if ($value){
            return $value;
        }else{
            return session('userId');
        }
    }



}