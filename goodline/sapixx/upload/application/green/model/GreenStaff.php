<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 工程师
 */
namespace app\green\model;
use think\Model;

class GreenStaff extends Model{


    /**
     * 回收员信息
     *
     * @return void
     */
    public function user(){
        return $this->hasOne('app\common\model\SystemUser','id','uid');
    }

    /**
     * @return \think\model\relation\HasOne
     * 运营商
     */
    public function operate(){
        return $this->hasOne('GreenOperate','id','operate_id');
    }
}