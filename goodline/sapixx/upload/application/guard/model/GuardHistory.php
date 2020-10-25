<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 管理
 */
namespace app\guard\model;
use think\Model;

class GuardHistory extends Model{
    
    protected $pk = 'id';
 
    //绑定帐号关系
    public function account(){
        return $this->hasOne('GuardUser','uid','uid');
    }

    //绑定所属社区
    public function guard(){
        return $this->hasOne('Guard','id','gid');
    }

}