<?php
namespace app\common\model;
// 赞记录模型
use think\Model;

class Focus extends Model {
	// 新增自动完成列表
//  protected $insert = ['uid'];


    public function User() {	//关联用户表
        return $this->hasOne('User', 'id', 'uid');
    }

    public function UserInfo()
    {
        return $this->hasOne('UserInfo', 'uid', 'uid');
    }



}