<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 判断是否VIP
 */
namespace app\popupshop\model;
use think\Model;

class Store extends Model{
    
    protected $pk     = 'id';
    protected $table  = 'ai_popupshop_store';

    /**
     * 所属用户信息
     * @return void
     */
    Public function user()
    {
        return $this->hasOne('app\common\model\SystemUser','id','uid');
    }
}