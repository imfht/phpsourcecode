<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 管理
 */
namespace app\guard\model;
use think\Model;

class Guard extends Model{
    
    protected $pk = 'id';
 
    //和主用户表绑定关系
    public function user(){
        return $this->hasOne('app\common\model\SystemUser','id','uid');
    }

}