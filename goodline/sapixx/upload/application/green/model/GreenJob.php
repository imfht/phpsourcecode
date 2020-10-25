<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 内容管理
 */
namespace app\green\model;
use think\Model;

class GreenJob extends Model{
    
    protected $autoWriteTimestamp = true;
    protected $createTime = false;


    //用户
    public function user(){
        return $this->hasOne('app\common\model\SystemUser','id','uid');
    }
}